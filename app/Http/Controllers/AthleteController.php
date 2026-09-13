<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAthleteRequest;
use App\Http\Requests\UpdateAthleteRequest;
use App\Models\Athlete;
use App\Models\ClassHistory;
use App\Models\SwimClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AthleteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Athlete::with(['currentClass', 'parentUser']);

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('current_class_id', $request->class_id);
        }

        // Filter by Kelompok Umur (KU)
        if ($request->filled('ku')) {
            $query->where('ku', $request->ku);
        }

        $athletes = $query->orderBy('name')->paginate(15)->withQueryString();

        $classes = SwimClass::orderBy('id')->get();
        
        // Get unique list of KU years from the database
        $kus = Athlete::select('ku')->distinct()->orderBy('ku', 'desc')->pluck('ku');

        return view('admin.athletes.index', compact('athletes', 'classes', 'kus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = SwimClass::orderBy('id')->get();
        $parents = User::where('role', 'user')->where('status', 'active')->orderBy('name')->get();

        return view('admin.athletes.create', compact('classes', 'parents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAthleteRequest $request)
    {
        $data = $request->validated();

        // Handle Photo Upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('athletes', 'public');
            $data['photo'] = $path;
        }

        $athlete = Athlete::create($data);

        // Record Initial Class History
        ClassHistory::create([
            'athlete_id' => $athlete->id,
            'class_id' => $athlete->current_class_id,
            'start_date' => $athlete->join_date,
            'changed_by' => auth()->id(),
            'notes' => 'Pendaftaran awal atlet ke kelas ' . $athlete->currentClass->name,
        ]);

        return redirect()->route('athletes.index')
            ->with('success', 'Atlet berhasil didaftarkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Athlete $athlete)
    {
        $athlete->load([
            'currentClass', 
            'parentUser', 
            'classHistories' => function ($q) {
                $q->with(['swimClass', 'changedBy'])->orderBy('start_date', 'desc');
            },
            'entries.competitionEvent.competition',
            'entries.result'
        ]);

        // Get all completed results
        $results = $athlete->entries
            ->filter(fn($entry) => $entry->result && $entry->result->status === 'finish')
            ->map(function($entry) {
                $evt = $entry->competitionEvent;
                $comp = $evt->competition;
                $res = $entry->result;
                $centiseconds = \App\Helpers\TimeHelper::toCentiseconds($res->time_result);

                return [
                    'entry_id' => $entry->id,
                    'event_id' => $evt->id,
                    'stroke' => $evt->stroke,
                    'distance' => $evt->distance,
                    'category' => $evt->category,
                    'event_label' => 'Gaya ' . ucfirst($evt->stroke) . ' - ' . $evt->distance . 'm (' . ($evt->category === 'perorangan' ? 'Perorangan' : 'Estafet Mix') . ')',
                    'event_key' => $evt->stroke . '_' . $evt->distance . '_' . $evt->category,
                    'competition_name' => $comp->name,
                    'competition_date' => $comp->start_date->format('Y-m-d'),
                    'time_result' => $res->time_result,
                    'centiseconds' => $centiseconds,
                    'rank' => $res->rank,
                ];
            });

        // Calculate Personal Best (PB) per event (combination of stroke, distance, and category)
        $personalBests = $results->groupBy('event_key')
            ->map(function($eventResults) {
                return $eventResults->sortBy('centiseconds')->first();
            })
            ->values()
            ->sortBy('event_label');

        // Prepare chronological data list for Chart.js
        $chartData = $results->sortBy('competition_date')
            ->values()
            ->groupBy('event_key');

        return view('admin.athletes.show', compact('athlete', 'personalBests', 'chartData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Athlete $athlete)
    {
        $classes = SwimClass::orderBy('id')->get();
        $parents = User::where('role', 'user')->where('status', 'active')->orderBy('name')->get();

        return view('admin.athletes.edit', compact('athlete', 'classes', 'parents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAthleteRequest $request, Athlete $athlete)
    {
        $data = $request->validated();
        $oldClassId = $athlete->current_class_id;

        // Handle Photo Upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($athlete->photo) {
                Storage::disk('public')->delete($athlete->photo);
            }
            $path = $request->file('photo')->store('athletes', 'public');
            $data['photo'] = $path;
        }

        $athlete->update($data);

        // Track Class History if Changed
        if ($oldClassId != $athlete->current_class_id) {
            // End the previous history
            $lastHistory = ClassHistory::where('athlete_id', $athlete->id)
                ->whereNull('end_date')
                ->orderBy('start_date', 'desc')
                ->first();

            if ($lastHistory) {
                $lastHistory->update(['end_date' => now()->toDateString()]);
            }

            // Create new history
            ClassHistory::create([
                'athlete_id' => $athlete->id,
                'class_id' => $athlete->current_class_id,
                'start_date' => now()->toDateString(),
                'changed_by' => auth()->id(),
                'notes' => $request->class_change_notes ?? 'Kenaikan/Perpindahan kelas latihan.',
            ]);

            // Create notification for mapped parent user account
            if ($athlete->parent_user_id) {
                $newClassName = \App\Models\SwimClass::find($athlete->current_class_id)->name ?? 'Kelas Baru';
                \App\Models\SystemNotification::create([
                    'user_id' => $athlete->parent_user_id,
                    'title' => '📢 Kenaikan Kelas Latihan',
                    'message' => "Selamat! Anak Anda {$athlete->name} telah dipindahkan ke kelas latihan {$newClassName} oleh Pelatih pada " . now()->format('d/m/Y') . ". Catatan Pelatih: " . ($request->class_change_notes ?? 'Kenaikan kelas.'),
                    'channel' => 'wa',
                    'sent_at' => now(),
                ]);
            }
        }

        return redirect()->route('athletes.show', $athlete->id)
            ->with('success', 'Data atlet berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Athlete $athlete)
    {
        // Delete photo if exists
        if ($athlete->photo) {
            Storage::disk('public')->delete($athlete->photo);
        }

        $athlete->delete();

        return redirect()->route('athletes.index')
            ->with('success', 'Atlet berhasil dihapus dari sistem.');
    }
}
