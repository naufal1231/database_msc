<?php

namespace App\Exports;

use App\Models\Competition;
use App\Models\CompetitionEntry;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FormA1Export implements FromView, ShouldAutoSize
{
    protected $competition;

    public function __construct(Competition $competition)
    {
        $this->competition = $competition;
    }

    public function view(): View
    {
        // Load individual entries for stroke: bebas, dada, kupu, punggung
        $entries = CompetitionEntry::whereHas('competitionEvent', function($q) {
            $q->where('competition_id', $this->competition->id)
              ->whereIn('stroke', ['bebas', 'dada', 'kupu', 'punggung'])
              ->where('category', 'perorangan');
        })->with(['athlete', 'competitionEvent', 'result'])->get();

        return view('admin.competitions.excel.form_a1', [
            'competition' => $this->competition,
            'entries' => $entries
        ]);
    }
}
