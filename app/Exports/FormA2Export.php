<?php

namespace App\Exports;

use App\Models\Competition;
use App\Models\CompetitionEntry;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FormA2Export implements FromView, ShouldAutoSize
{
    protected $competition;

    public function __construct(Competition $competition)
    {
        $this->competition = $competition;
    }

    public function view(): View
    {
        // Load individual entries for Medley (ganti)
        $entries = CompetitionEntry::whereHas('competitionEvent', function($q) {
            $q->where('competition_id', $this->competition->id)
              ->where('stroke', 'ganti')
              ->where('category', 'perorangan');
        })->with(['athlete', 'competitionEvent', 'result'])->get();

        return view('admin.competitions.excel.form_a2', [
            'competition' => $this->competition,
            'entries' => $entries
        ]);
    }
}
