<?php

namespace App\Http\Controllers;

use App\Models\CreditLedger;
use App\Models\Scan;
use App\Models\Team;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'users' => User::count(),
            'teams' => Team::count(),
            'scans' => Scan::count(),
            'credits' => (int) CreditLedger::sum('amount'),
            'queuedScans' => Scan::whereIn('status', ['queued', 'processing'])->count(),
            'completedScans' => Scan::where('status', 'completed')->count(),
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentScans' => Scan::with(['team', 'user'])->latest()->limit(8)->get(),
            'recentTeams' => Team::with('owner')->latest()->limit(6)->get(),
            'recentLedgerEntries' => CreditLedger::with(['team', 'user'])->latest()->limit(8)->get(),
        ]);
    }
}
