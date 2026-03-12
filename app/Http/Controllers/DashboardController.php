<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $team = $request->user()->currentTeam;

        abort_unless($team, 403);

        $team->load([
            'scans' => fn ($query) => $query->latest()->limit(5),
            'creditLedgers' => fn ($query) => $query->latest()->limit(5),
        ]);

        $scanCounts = [
            'total' => $team->scans()->count(),
            'queued' => $team->scans()->whereIn('status', ['queued', 'processing'])->count(),
            'completed' => $team->scans()->where('status', 'completed')->count(),
        ];

        return view('dashboard', [
            'team' => $team,
            'recentScans' => $team->scans,
            'recentCredits' => $team->creditLedgers,
            'scanCounts' => $scanCounts,
        ]);
    }
}
