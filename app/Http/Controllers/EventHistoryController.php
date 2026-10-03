<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\SwimClass;
use App\Models\CompetitionEntry;
use App\Helpers\TimeHelper;
use Illuminate\Http\Request;

class EventHistoryController extends Controller
{
    /**
     * Index: list all athletes (admin/super_admin only)
     */
    public function index(Request $request)
    {
        $query = Athlete::with(['currentClass', 'parentUser']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('class_id')) {
            $query->where('current_class_id', $request->class_id);
        }
        if ($request->filled('ku')) {
            $query->where('ku', $request->ku);
        }

        $athletes = $query->orderBy('name')->paginate(20)->withQueryString();

        // Preload stats: total events + PB counts
        $athleteIds = $athletes->pluck('id')->toArray();
        $entriesWithResults = CompetitionEntry::whereIn('athlete_id', $athleteIds)
            ->whereHas('result', fn($q) => $q->where('status', 'finish'))
            ->with(['competitionEvent', 'result'])
            ->get();

        $athleteStats = [];
        foreach ($entriesWithResults as $entry) {
            $aid      = $entry->athlete_id;
            $eventKey = $entry->competitionEvent->stroke . '_'
                      . $entry->competitionEvent->distance . '_'
                      . $entry->competitionEvent->category;
            $cs       = TimeHelper::toCentiseconds($entry->result->time_result);

            if (!isset($athleteStats[$aid])) {
                $athleteStats[$aid] = ['total_events' => 0, 'best_results' => []];
            }
            if (!isset($athleteStats[$aid]['best_results'][$eventKey])
                || $cs < $athleteStats[$aid]['best_results'][$eventKey]) {
                $athleteStats[$aid]['best_results'][$eventKey] = $cs;
            }
            $athleteStats[$aid]['total_events']++;
        }

        foreach ($athletes as $athlete) {
            $athlete->total_events = $athleteStats[$athlete->id]['total_events'] ?? 0;
            $athlete->total_pbs    = count($athleteStats[$athlete->id]['best_results'] ?? []);
        }

        $classes = SwimClass::orderBy('id')->get();
        $kus     = Athlete::select('ku')->distinct()->orderBy('ku', 'desc')->pluck('ku');

        return view('admin.event-history.index', compact('athletes', 'classes', 'kus'));
    }

    /**
     * Show event history for a specific athlete (admin route).
     */
    public function show(Athlete $athlete)
    {
        $athlete->load(['currentClass', 'parentUser']);
        [$historyRows, $historyByEvent, $currentPBs] = $this->buildHistory($athlete);

        return view('admin.event-history.show', compact('athlete', 'historyRows', 'historyByEvent', 'currentPBs'));
    }

    /**
     * Parent-facing: show event history for the authenticated parent's child.
     */
    public function parentShow(Request $request, Athlete $athlete)
    {
        // Security: ensure athlete belongs to authenticated parent
        if ($athlete->parent_user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke data atlet ini.');
        }

        $athlete->load(['currentClass', 'parentUser']);
        [$historyRows, $historyByEvent, $currentPBs] = $this->buildHistory($athlete);

        return view('parent.event-history.show', compact('athlete', 'historyRows', 'historyByEvent', 'currentPBs'));
    }

    /**
     * Core history builder — used by both admin and parent routes.
     *
     * Returns [ $historyRows, $historyByEvent, $currentPBs ]
     *
     * $historyRows: flat array, sorted by competition_date DESC, then entry id ASC
     *   Each row: [
     *     'entry_id', 'competition_id', 'competition_name', 'competition_date' (Y-m-d),
     *     'competition_date_formatted' (d/m/Y), 'stroke', 'distance', 'category',
     *     'event_label', 'event_key', 'time_result', 'centiseconds', 'rank',
     *     'result_status',  // 'finish' | 'DNS' | 'DQ' | null (no result)
     *     'pb_before_event', 'pb_centiseconds', 'difference', 'is_faster',
     *     'is_first_valid_result', // true if no prior valid time for this combo
     *   ]
     *
     * $historyByEvent: grouped by event_key with sub-history arrays
     * $currentPBs: best finish time per event_key
     */
    private function buildHistory(Athlete $athlete): array
    {
        // 1. Eager-load all entries (including DNS/DQ, all statuses) with results
        $entries = CompetitionEntry::where('athlete_id', $athlete->id)
            ->with(['competitionEvent.competition', 'result'])
            ->get();

        // 2. Build flat data list from entries that have any result
        $allRows = [];
        foreach ($entries as $entry) {
            $evt  = $entry->competitionEvent;
            $comp = $evt->competition;
            $res  = $entry->result;

            // Only show rows that have a result record OR that are DNS/DQ status on entry
            // (Results can have DNS/DQ status too)
            if (!$res) {
                continue; // No result recorded at all — skip
            }

            $eventKey = "{$evt->stroke}_{$evt->distance}_{$evt->category}";
            $isFinish = ($res->status === 'finish');

            $allRows[] = [
                'entry_id'                  => $entry->id,
                'competition_id'            => $comp->id,
                'competition_name'          => $comp->name,
                'competition_date'          => $comp->start_date->format('Y-m-d'),
                'competition_date_formatted'=> $comp->start_date->format('d/m/Y'),
                'competition_date_ts'       => $comp->start_date->timestamp,
                'stroke'                    => $evt->stroke,
                'distance'                  => $evt->distance,
                'category'                  => $evt->category,
                'event_label'               => $this->buildEventLabel($evt->stroke, $evt->distance, $evt->category),
                'event_key'                 => $eventKey,
                'time_result'               => $isFinish ? $res->time_result : null,
                'centiseconds'              => $isFinish ? TimeHelper::toCentiseconds($res->time_result) : null,
                'rank'                      => $res->rank,
                'result_status'             => $res->status,
                'pb_before_event'           => null, // filled below
                'pb_centiseconds'           => null,
                'difference'                => null,
                'is_faster'                 => null,
                'is_first_valid_result'     => false,
            ];
        }

        // 3. Sort: competition_date ASC (oldest first) for PB calculation,
        //    then tie-break by entry_id ASC so same-day ordering is deterministic
        usort($allRows, function ($a, $b) {
            if ($a['competition_date'] !== $b['competition_date']) {
                return strcmp($a['competition_date'], $b['competition_date']); // ASC for PB calc
            }
            return $a['entry_id'] <=> $b['entry_id'];
        });

        // 4. Calculate historical PB for each row
        //    For each row, PB = best finish time with the same event_key
        //    and competition_date < current row's competition_date
        //    (if same date: only earlier entry_ids count as "before")
        $historicalPBPerKey = []; // eventKey => ['time' => string, 'cs' => int]

        foreach ($allRows as &$row) {
            $key = $row['event_key'];
            $currentDateStr = $row['competition_date'];
            $currentEntryId = $row['entry_id'];

            // Determine best finish time before this row
            $pbCs   = null;
            $pbTime = null;
            foreach ($allRows as $other) {
                if ($other['event_key'] !== $key)       continue;
                if ($other['result_status'] !== 'finish') continue;
                if ($other['centiseconds'] === null)      continue;

                // "Before" = earlier date, OR same date with smaller entry_id
                $isBefore = ($other['competition_date'] < $currentDateStr)
                    || ($other['competition_date'] === $currentDateStr && $other['entry_id'] < $currentEntryId);

                if ($isBefore) {
                    if ($pbCs === null || $other['centiseconds'] < $pbCs) {
                        $pbCs   = $other['centiseconds'];
                        $pbTime = $other['time_result'];
                    }
                }
            }

            $row['pb_before_event']       = $pbTime;
            $row['pb_centiseconds']       = $pbCs;
            $row['is_first_valid_result'] = ($pbTime === null) && ($row['result_status'] === 'finish');

            // 5. Calculate difference (only for finish results with a prior PB)
            if ($row['result_status'] === 'finish' && $row['centiseconds'] !== null && $pbCs !== null) {
                $diffCs          = abs($row['centiseconds'] - $pbCs);
                $row['difference'] = TimeHelper::fromCentiseconds($diffCs);
                $row['is_faster']  = $row['centiseconds'] < $pbCs;
                // Equal time: is_faster=false, difference='00.00.00'
            }
        }
        unset($row);

        // 6. Sort final rows: competition_date DESC (newest first) for display
        usort($allRows, function ($a, $b) {
            if ($a['competition_date'] !== $b['competition_date']) {
                return strcmp($b['competition_date'], $a['competition_date']); // DESC
            }
            return $b['entry_id'] <=> $a['entry_id'];
        });

        // 7. Build current PBs (best finish time per event_key across all history)
        $currentPBs = [];
        foreach ($allRows as $row) {
            if ($row['result_status'] !== 'finish' || $row['centiseconds'] === null) continue;
            $key = $row['event_key'];
            if (!isset($currentPBs[$key]) || $row['centiseconds'] < $currentPBs[$key]['centiseconds']) {
                $currentPBs[$key] = [
                    'event_label'      => $row['event_label'],
                    'stroke'           => $row['stroke'],
                    'distance'         => $row['distance'],
                    'category'         => $row['category'],
                    'time_result'      => $row['time_result'],
                    'centiseconds'     => $row['centiseconds'],
                    'competition_name' => $row['competition_name'],
                    'competition_date' => $row['competition_date_formatted'],
                ];
            }
        }

        // 8. Group by event_key for tabbed/section view
        $historyByEvent = [];
        foreach ($allRows as $row) {
            $key = $row['event_key'];
            if (!isset($historyByEvent[$key])) {
                $historyByEvent[$key] = [
                    'event_label' => $row['event_label'],
                    'stroke'      => $row['stroke'],
                    'distance'    => $row['distance'],
                    'history'     => [],
                ];
            }
            $historyByEvent[$key]['history'][] = $row;
        }

        return [$allRows, $historyByEvent, $currentPBs];
    }

    private function buildEventLabel(string $stroke, int $distance, string $category): string
    {
        $strokeMap = [
            'bebas'     => 'Bebas',
            'dada'      => 'Dada',
            'kupu'      => 'Kupu-Kupu',
            'punggung'  => 'Punggung',
            'ganti'     => 'Ganti',
            'estafet'   => 'Estafet',
        ];
        $strokeLabel    = $strokeMap[$stroke] ?? ucfirst($stroke);
        $categoryLabel  = $category === 'perorangan' ? 'Perorangan' : 'Estafet Mix';
        return "{$distance}m {$strokeLabel} ({$categoryLabel})";
    }
}