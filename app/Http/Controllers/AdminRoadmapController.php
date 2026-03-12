<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AdminRoadmapController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.roadmap', [
            'areas' => [
                [
                    'title' => 'Platform controls',
                    'summary' => 'Give the operator visibility into users, teams, credits, scans, and support actions.',
                    'tasks' => [
                        ['title' => 'Admin dashboard and summary metrics', 'complete' => true],
                        ['title' => 'Admin roadmap / memory page', 'complete' => true],
                        ['title' => 'Team and customer management tools', 'complete' => false],
                        ['title' => 'Manual credit adjustment tools', 'complete' => false],
                    ],
                ],
                [
                    'title' => 'Billing operations',
                    'summary' => 'Track revenue, subscriptions, credit top-ups, and failed payments from the operator side.',
                    'tasks' => [
                        ['title' => 'Stripe billing and top-up implementation', 'complete' => false],
                        ['title' => 'Billing overview inside admin', 'complete' => false],
                        ['title' => 'Refund / reconciliation support workflow', 'complete' => false],
                    ],
                ],
                [
                    'title' => 'Python brain delivery',
                    'summary' => 'Build and monitor the analysis pipeline that powers scan results and customer notifications.',
                    'tasks' => [
                        ['title' => 'FastAPI bridge and callback contract', 'complete' => false],
                        ['title' => 'Queue worker and scan handoff', 'complete' => false],
                        ['title' => 'LLM-driven gap analysis and ranking', 'complete' => false],
                        ['title' => 'Report write-back into Laravel', 'complete' => false],
                        ['title' => 'Scan-ready notifications', 'complete' => false],
                        ['title' => 'Worker health and incident monitoring', 'complete' => false],
                    ],
                ],
            ],
        ]);
    }
}
