<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\SwimClass;
use App\Helpers\TimeHelper;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Show athlete progress reports.
     */
    public function index(Request $request)
    {
        $classes = SwimClass::all();
        
        // Get unique birth years (KU)
        $kus = Athlete::selectRaw('YEAR(birth_date) as ku')
            ->distinct()
            ->orderBy('ku')
            ->get()
            ->pluck('ku');

        // Apply filters
        $query = Athlete::with(['currentClass', 'entries.competitionEvent', 'entries.result']);

        if ($request->filled('class_id')) {
            $query->where('current_class_id', $request->class_id);
        }

        if ($request->filled('ku')) {
            $query->whereRaw('YEAR(birth_date) = ?', [$request->ku]);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $athletes = $query->orderBy('name')->get();

        // Calculate performance rekap for each athlete
        $reportData = $athletes->map(function ($athlete) {
            $finishedEntries = $athlete->entries->filter(
                fn($entry) => $entry->result && $entry->result->status === 'finish'
            );

            // Build ALL personal bests grouped by event (stroke + distance + category)
            $allResults = $finishedEntries->map(function ($entry) {
                $evt = $entry->competitionEvent;
                $res = $entry->result;
                $comp = $evt->competition ?? null;
                return [
                    'stroke'           => $evt->stroke,
                    'distance'         => $evt->distance,
                    'category'         => $evt->category,
                    'event_label'      => 'Gaya ' . ucfirst($evt->stroke) . ' ' . $evt->distance . 'm (' . ($evt->category === 'perorangan' ? 'Perorangan' : 'Estafet') . ')',
                    'event_key'        => $evt->stroke . '_' . $evt->distance . '_' . $evt->category,
                    'time_result'      => $res->time_result,
                    'cs'               => TimeHelper::toCentiseconds($res->time_result),
                    'competition_name' => $comp ? $comp->name : '-',
                    'competition_date' => $comp ? $comp->start_date->format('d/m/Y') : '-',
                ];
            });

            // Get PB per event (lowest centiseconds)
            $personalBests = $allResults
                ->groupBy('event_key')
                ->map(fn($group) => $group->sortBy('cs')->first())
                ->values()
                ->sortBy('event_label')
                ->values();

            return [
                'id'             => $athlete->id,
                'name'           => $athlete->name,
                'ku'             => $athlete->ku,
                'gender'         => $athlete->gender === 'L' ? 'PA' : 'PI',
                'class_name'     => $athlete->currentClass->name,
                'races_count'    => $finishedEntries->count(),
                'personal_bests' => $personalBests, // All PBs for modal
            ];
        });

        // Determine if requesting print-friendly preview
        if ($request->query('print') === '1') {
            return view('admin.reports.print', [
                'reportData'     => $reportData,
                'selectedClass'  => $request->filled('class_id') ? SwimClass::find($request->class_id) : null,
                'selectedKu'     => $request->ku,
                'selectedGender' => $request->gender,
            ]);
        }

        return view('admin.reports.index', compact('classes', 'kus', 'reportData'));
    }
}
