<?php

namespace App\Exports;

use App\Models\Competition;
use App\Models\CompetitionEntry;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FormA3Export implements FromView, ShouldAutoSize
{
    protected $competition;

    public function __construct(Competition $competition)
    {
        $this->competition = $competition;
    }

    public function view(): View
    {
        // Load entries for relays (estafet)
        $entries = CompetitionEntry::whereHas('competitionEvent', function($q) {
            $q->where('competition_id', $this->competition->id)
              ->where(function($sub) {
                  $sub->where('stroke', 'estafet')
                      ->orWhere('category', 'estafet_mix');
              });
        })->with(['athlete', 'competitionEvent', 'result'])->get();

        return view('admin.competitions.excel.form_a3', [
            'competition' => $this->competition,
            'entries' => $entries
        ]);
    }
}
