<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Competition;
use App\Models\CompetitionEvent;
use App\Models\CompetitionEntry;
use App\Models\SwimClass;
use Illuminate\Http\Request;

class CompetitionEntryController extends Controller
{
    public function create(Competition $competition, Request $request)
    {
        $events = CompetitionEvent::where('competition_id', $competition->id)
            ->orderBy('stroke')
            ->orderBy('distance')
            ->get();

        $query = Athlete::with('currentClass');

        // Apply Class Filter
        if ($request->filled('class_id')) {
            $query->where('current_class_id', $request->class_id);
        }

        // Apply KU Filter
        if ($request->filled('ku')) {
            $query->where('ku', $request->ku);
        }

        $athletes = $query->orderBy('name')->get();

        // Get already registered entries for checkbox initialization
        $eventIds = $events->pluck('id')->toArray();
        $existingEntries = CompetitionEntry::whereIn('competition_event_id', $eventIds)
            ->get()
            ->groupBy('athlete_id');

        // Fetch personal bests for these athletes
        $athleteIds = $athletes->pluck('id')->toArray();
        $allFinishedEntries = CompetitionEntry::whereIn('athlete_id', $athleteIds)
            ->whereHas('result', function($q) {
                $q->where('status', 'finish');
            })
            ->with(['result', 'competitionEvent'])
            ->get();

        $athletePbs = [];
        foreach ($allFinishedEntries as $entry) {
            $athleteId = $entry->athlete_id;
            $evt = $entry->competitionEvent;
            $eventKey = $evt->stroke . '_' . $evt->distance . '_' . $evt->category;
            $timeResult = $entry->result->time_result;
            $cs = \App\Helpers\TimeHelper::toCentiseconds($timeResult);

            if (!isset($athletePbs[$athleteId][$eventKey])) {
                $athletePbs[$athleteId][$eventKey] = [
                    'time' => $timeResult,
                    'cs' => $cs
                ];
            } else {
                if ($cs < $athletePbs[$athleteId][$eventKey]['cs']) {
                    $athletePbs[$athleteId][$eventKey] = [
                        'time' => $timeResult,
                        'cs' => $cs
                    ];
                }
            }
        }

        // Simplify to event_key => time
        foreach ($athletePbs as $athleteId => $eventsData) {
            foreach ($eventsData as $eventKey => $data) {
                $athletePbs[$athleteId][$eventKey] = $data['time'];
            }
        }

        $classes = SwimClass::orderBy('id')->get();
        $kus = Athlete::select('ku')->distinct()->orderBy('ku', 'desc')->pluck('ku');

        return view('admin.competitions.entries', compact(
            'competition', 'events', 'athletes', 'existingEntries', 'classes', 'kus', 'athletePbs'
        ));
    }

    public function store(Competition $competition, Request $request)
    {
        $events = CompetitionEvent::where('competition_id', $competition->id)->get();
        $eventIds = $events->pluck('id')->toArray();

        // Athlete IDs displayed in current form submission
        $displayedAthleteIds = $request->input('displayed_athlete_ids', []);
        $postedEntries = $request->input('entries', []);
        $postedSeedTimes = $request->input('seed_times', []);

        // Fetch personal bests for validation
        $allFinishedEntries = CompetitionEntry::whereIn('athlete_id', $displayedAthleteIds)
            ->whereHas('result', function($q) {
                $q->where('status', 'finish');
            })
            ->with(['result', 'competitionEvent'])
            ->get();

        $athletePbs = [];
        foreach ($allFinishedEntries as $entry) {
            $athleteId = $entry->athlete_id;
            $evt = $entry->competitionEvent;
            $eventKey = $evt->stroke . '_' . $evt->distance . '_' . $evt->category;
            $timeResult = $entry->result->time_result;
            $cs = \App\Helpers\TimeHelper::toCentiseconds($timeResult);

            if (!isset($athletePbs[$athleteId][$eventKey])) {
                $athletePbs[$athleteId][$eventKey] = [
                    'cs' => $cs
                ];
            } else {
                if ($cs < $athletePbs[$athleteId][$eventKey]['cs']) {
                    $athletePbs[$athleteId][$eventKey] = [
                        'cs' => $cs
                    ];
                }
            }
        }

        // Validate that each checked entry has a seed time if they don't have a PB
        foreach ($displayedAthleteIds as $athleteId) {
            $selectedEventIds = isset($postedEntries[$athleteId]) ? $postedEntries[$athleteId] : [];
            $athlete = Athlete::find($athleteId);
            
            foreach ($selectedEventIds as $eventId) {
                $evt = CompetitionEvent::find($eventId);
                $eventKey = $evt->stroke . '_' . $evt->distance . '_' . $evt->category;
                $hasPb = isset($athletePbs[$athleteId][$eventKey]);
                
                $seedTime = isset($postedSeedTimes[$athleteId][$eventId]) ? trim($postedSeedTimes[$athleteId][$eventId]) : '';
                
                if (!$hasPb && empty($seedTime)) {
                    return back()->withInput()->with('error', "Atlet {$athlete->name} belum memiliki catatan waktu untuk Gaya " . ucfirst($evt->stroke) . " {$evt->distance}m. Limit/Seed Time wajib diisi!");
                }
            }
        }

        foreach ($displayedAthleteIds as $athleteId) {
            $selectedEventIds = isset($postedEntries[$athleteId]) ? $postedEntries[$athleteId] : [];

            // 1. Add/Update entries
            foreach ($selectedEventIds as $eventId) {
                $seedTime = isset($postedSeedTimes[$athleteId][$eventId]) ? trim($postedSeedTimes[$athleteId][$eventId]) : null;

                CompetitionEntry::updateOrCreate([
                    'athlete_id' => $athleteId,
                    'competition_event_id' => $eventId,
                ], [
                    'seed_time' => $seedTime ?: null
                ]);
            }

            // 2. Delete unchecked entries for this athlete in this competition
            CompetitionEntry::where('athlete_id', $athleteId)
                ->whereIn('competition_event_id', $eventIds)
                ->whereNotIn('competition_event_id', $selectedEventIds)
                ->delete();
        }

        return redirect()->route('competitions.show', $competition->id)
            ->with('success', 'Pendaftaran atlet dan nomor lomba berhasil diperbarui.');
    }
}
