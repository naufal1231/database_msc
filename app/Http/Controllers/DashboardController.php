<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Competition;
use App\Models\SwimClass;
use App\Models\User;
use App\Helpers\TimeHelper;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->user()->role;

        if ($role === 'super_admin') {
            return redirect()->route('super_admin.dashboard');
        } elseif ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('parent.dashboard');
        }
    }

    /**
     * Coach (Super Admin) Dashboard
     */
    public function superAdminDashboard()
    {
        $stats = [
            'athletes_count' => Athlete::count(),
            'classes_count' => SwimClass::count(),
            'competitions_count' => Competition::count(),
        ];

        // Get parent registration requests
        $pendingParents = User::where('role', 'user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('super-admin.dashboard', compact('stats', 'pendingParents'));
    }

    /**
     * Coach (Admin) Dashboard
     */
    public function adminDashboard()
    {
        $stats = [
            'athletes_count' => Athlete::count(),
            'classes_count' => SwimClass::count(),
            'competitions_count' => Competition::count(),
        ];

        // Get parent registration requests
        $pendingParents = User::where('role', 'user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingParents'));
    }

    /**
     * Parents (User) Dashboard
     */
    public function parentDashboard(Request $request)
    {
        // Get all children registered for this parent account
        $children = Athlete::where('parent_user_id', auth()->id())
            ->with('currentClass')
            ->orderBy('name')
            ->get();

        $selectedChild = null;
        $personalBests = collect();
        $chartData = collect();

        if ($children->count() > 0) {
            $childId = $request->query('child_id', $children->first()->id);
            
            // Scope protection: ensure the child belongs to the logged-in parent
            $selectedChild = $children->firstWhere('id', $childId) ?: $children->first();

            // Load details for the selected child
            $selectedChild->load([
                'classHistories' => function($q) {
                    $q->with(['swimClass', 'changedBy'])->orderBy('start_date', 'desc');
                },
                'entries.competitionEvent.competition',
                'entries.result'
            ]);

            // Calculate completed time results
            $results = $selectedChild->entries
                ->filter(fn($entry) => $entry->result && $entry->result->status === 'finish')
                ->map(function($entry) {
                    $evt = $entry->competitionEvent;
                    $comp = $evt->competition;
                    $res = $entry->result;
                    $centiseconds = TimeHelper::toCentiseconds($res->time_result);

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

            // Group and select the fastest record for each event type (PB)
            $personalBests = $results->groupBy('event_key')
                ->map(fn($eventResults) => $eventResults->sortBy('centiseconds')->first())
                ->values()
                ->sortBy('event_label');

            // Format chronological data for progress tracking charts
            $chartData = $results->sortBy('competition_date')
                ->values()
                ->groupBy('event_key');
        }

        // Get recent notifications for this parent user
        $notifications = \App\Models\SystemNotification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('parent.dashboard', compact('children', 'selectedChild', 'personalBests', 'chartData', 'notifications'));
    }
}
