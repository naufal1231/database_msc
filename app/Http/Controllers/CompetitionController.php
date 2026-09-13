<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionEvent;
use Illuminate\Http\Request;
use App\Exports\FormA1Export;
use App\Exports\FormA2Export;
use App\Exports\FormA3Export;
use Maatwebsite\Excel\Facades\Excel;

class CompetitionController extends Controller
{
    public function index()
    {
        $competitions = Competition::orderBy('start_date', 'desc')->get();
        return view('admin.competitions.index', compact('competitions'));
    }

    public function create()
    {
        return view('admin.competitions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'organizer' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:akan_datang,berlangsung,selesai'],
        ]);

        $competition = Competition::create($data);

        // Auto Create Default Events if checked
        if ($request->has('auto_create_events')) {
            $defaultEvents = [
                // Gaya Bebas
                ['stroke' => 'bebas', 'distance' => 50, 'category' => 'perorangan'],
                ['stroke' => 'bebas', 'distance' => 100, 'category' => 'perorangan'],
                ['stroke' => 'bebas', 'distance' => 200, 'category' => 'perorangan'],
                // Gaya Dada
                ['stroke' => 'dada', 'distance' => 50, 'category' => 'perorangan'],
                ['stroke' => 'dada', 'distance' => 100, 'category' => 'perorangan'],
                // Gaya Kupu
                ['stroke' => 'kupu', 'distance' => 50, 'category' => 'perorangan'],
                ['stroke' => 'kupu', 'distance' => 100, 'category' => 'perorangan'],
                // Gaya Punggung
                ['stroke' => 'punggung', 'distance' => 50, 'category' => 'perorangan'],
                ['stroke' => 'punggung', 'distance' => 100, 'category' => 'perorangan'],
                // Gaya Ganti
                ['stroke' => 'ganti', 'distance' => 200, 'category' => 'perorangan'],
                // Estafet Mix (4x50m)
                ['stroke' => 'estafet', 'distance' => 50, 'category' => 'estafet_mix'],
            ];

            foreach ($defaultEvents as $evt) {
                CompetitionEvent::create([
                    'competition_id' => $competition->id,
                    'stroke' => $evt['stroke'],
                    'distance' => $evt['distance'],
                    'category' => $evt['category'],
                ]);
            }
        }

        return redirect()->route('competitions.show', $competition->id)
            ->with('success', 'Event perlombaan berhasil dibuat.');
    }

    public function show(Competition $competition)
    {
        $competition->load(['events' => function($q) {
            $q->orderBy('stroke')->orderBy('distance');
        }]);
        
        return view('admin.competitions.show', compact('competition'));
    }

    public function destroy(Competition $competition)
    {
        $competition->delete();
        return redirect()->route('competitions.index')
            ->with('success', 'Event perlombaan berhasil dihapus.');
    }

    public function exportExcel(Competition $competition, Request $request)
    {
        $form = $request->query('form');

        if ($form === 'A1') {
            return Excel::download(new FormA1Export($competition), "[Form A1] {$competition->name}.xlsx");
        } elseif ($form === 'A2') {
            return Excel::download(new FormA2Export($competition), "[Form A2] {$competition->name}.xlsx");
        } elseif ($form === 'A3') {
            return Excel::download(new FormA3Export($competition), "[Form A3] {$competition->name}.xlsx");
        }

        return redirect()->back()->with('error', 'Tipe formulir ekspor tidak valid.');
    }
}
