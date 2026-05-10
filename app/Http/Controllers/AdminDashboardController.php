<?php

namespace App\Http\Controllers;

use App\Models\CreditLedger;
use App\Models\Scan;
use App\Models\ScanReport;
use App\Models\Team;
use App\Models\User;
use App\Services\Monitoring\EngineHealthService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(EngineHealthService $engineHealth): View
    {
        $stats = [
            'users' => User::count(),
            'teams' => Team::count(),
            'scans' => Scan::count(),
            'credits' => (int) CreditLedger::sum('amount'),
            'queuedScans' => Scan::whereIn('status', ['queued', 'processing'])->count(),
            'completedScans' => Scan::where('status', 'completed')->count(),
        ];
        $reports = ScanReport::query()->with('scan.team')->latest()->get();
        $qualityAlerts = $reports
            ->filter(fn (ScanReport $report) => $report->feedback_rating === 'not_helpful' || $report->qualityLoopScore() < 60)
            ->take(5)
            ->values();
        $queueStats = [
            'backlog' => Schema::hasTable('jobs') ? (int) DB::table('jobs')->count() : 0,
            'failedJobs' => Schema::hasTable('failed_jobs') ? (int) DB::table('failed_jobs')->count() : 0,
            'dispatchingScans' => Scan::where('status', 'dispatching')->count(),
            'processingScans' => Scan::where('status', 'processing')->count(),
            'failedScansToday' => Scan::where('status', 'failed')
                ->where('failed_at', '>=', now()->subDay())
                ->count(),
        ];
        $qualityStats = [
            'reports' => $reports->count(),
            'averageQuality' => (int) round($reports->avg(fn (ScanReport $report) => $report->qualityLoopScore()) ?? 0),
            'averageConfidence' => (int) round($reports->avg(fn (ScanReport $report) => $report->confidenceScore()) ?? 0),
            'helpful' => $reports->where('feedback_rating', 'helpful')->count(),
            'notHelpful' => $reports->where('feedback_rating', 'not_helpful')->count(),
            'awaitingFeedback' => $reports->filter(fn (ScanReport $report) => blank($report->feedback_rating))->count(),
            'needsReview' => $reports->filter(fn (ScanReport $report) => $report->qualityLoopState() === 'needs_review')->count(),
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'engineHealth' => $engineHealth->snapshot(),
            'queueStats' => $queueStats,
            'qualityStats' => $qualityStats,
            'qualityAlerts' => $qualityAlerts,
            'recentScans' => Scan::with(['team', 'user'])->latest()->limit(8)->get(),
            'recentTeams' => Team::with('owner')->latest()->limit(6)->get(),
            'recentLedgerEntries' => CreditLedger::with(['team', 'user'])->latest()->limit(8)->get(),
        ]);
    }
}
