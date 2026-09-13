<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionEvent;
use App\Models\CompetitionEntry;
use App\Models\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function edit(Competition $competition)
    {
        $events = CompetitionEvent::where('competition_id', $competition->id)
            ->orderBy('stroke')
            ->orderBy('distance')
            ->get();

        // Get all entries with athletes and results
        $eventIds = $events->pluck('id')->toArray();
        $entries = CompetitionEntry::whereIn('competition_event_id', $eventIds)
            ->with(['athlete', 'competitionEvent', 'result'])
            ->get()
            ->groupBy('competition_event_id');

        return view('admin.competitions.results', compact('competition', 'events', 'entries'));
    }

    public function store(Competition $competition, Request $request)
    {
        $request->validate([
            'results' => ['required', 'array'],
            'results.*.status' => ['required', 'in:finish,DQ,DNS'],
        ]);

        $resultsData = $request->input('results', []);
        $validationErrors = [];

        // Conditional manual validation for time format
        foreach ($resultsData as $entryId => $data) {
            if ($data['status'] === 'finish') {
                if (empty($data['time_result'])) {
                    $validationErrors["results.{$entryId}.time_result"] = "Waktu wajib diisi jika status Finish.";
                } elseif (!preg_match('/^\d{2}\.\d{2}\.\d{2}$/', $data['time_result'])) {
                    $validationErrors["results.{$entryId}.time_result"] = "Format waktu harus 6 digit (Contoh: 00.32.94).";
                }
            }
        }

        if (!empty($validationErrors)) {
            return redirect()->back()
                ->withErrors($validationErrors)
                ->withInput()
                ->with('error', 'Terdapat kesalahan input format waktu.');
        }

        // Save Results
        foreach ($resultsData as $entryId => $data) {
            $timeResult = ($data['status'] === 'finish') ? $data['time_result'] : '00.00.00';
            $rank = null; // Rank tidak lagi diinput secara manual

            // Update or Create Result record
            Result::updateOrCreate(
                ['competition_entry_id' => $entryId],
                [
                    'time_result' => $timeResult,
                    'rank' => $rank,
                    'status' => $data['status'],
                    'input_by' => auth()->id(),
                ]
            );

            // Sync status to entry
            $entry = CompetitionEntry::with('athlete')->find($entryId);
            if ($entry) {
                $entryStatus = in_array($data['status'], ['DNS', 'DQ']) ? $data['status'] : 'terdaftar';
                $entry->update(['status' => $entryStatus]);

                // Create notification for mapped parent user account
                if ($entry->athlete && $entry->athlete->parent_user_id) {
                    $evt = $entry->competitionEvent;
                    $comp = $evt->competition;
                    $strokeLabel = 'Gaya ' . ucfirst($evt->stroke) . ' - ' . $evt->distance . 'm';
                    $resultStatus = strtoupper($data['status']);
                    
                    $messageDetail = ($data['status'] === 'finish') 
                        ? "Waktu: {$timeResult} (Peringkat " . ($rank ?? '-') . ")"
                        : "Status: {$resultStatus}";

                    \App\Models\SystemNotification::create([
                        'user_id' => $entry->athlete->parent_user_id,
                        'title' => '⏱️ Hasil Waktu Lomba Baru',
                        'message' => "Catatan waktu baru untuk {$entry->athlete->name} pada event {$comp->name} - Nomor {$strokeLabel} telah dirilis: {$messageDetail}.",
                        'channel' => 'wa',
                        'sent_at' => now(),
                    ]);
                }
            }
        }

        return redirect()->route('competitions.show', $competition->id)
            ->with('success', 'Catatan waktu perlombaan berhasil disimpan.');
    }
}
