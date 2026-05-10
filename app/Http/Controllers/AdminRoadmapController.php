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
                        ['title' => 'Human end-to-end system test plan', 'complete' => true],
                        ['title' => 'Team and customer management tools', 'complete' => false],
                        ['title' => 'Manual credit adjustment tools', 'complete' => false],
                    ],
                ],
                [
                    'title' => 'Billing operations',
                    'summary' => 'Track revenue, subscriptions, credit top-ups, and failed payments from the operator side.',
                    'tasks' => [
                        ['title' => 'Stripe billing and top-up implementation', 'complete' => true],
                        ['title' => 'Billing overview inside admin', 'complete' => false],
                        ['title' => 'Refund / reconciliation support workflow', 'complete' => false],
                    ],
                ],
                [
                    'title' => 'Python brain delivery',
                    'summary' => 'The FastAPI engine now runs the full v1 scan loop: evidence collection, schema audit, buyer-friction intelligence, Missing 3 synthesis, ranking, notifications, and operator monitoring.',
                    'tasks' => [
                        ['title' => 'FastAPI bridge and callback contract', 'complete' => true],
                        ['title' => 'Queue worker and scan handoff', 'complete' => true],
                        ['title' => 'LLM-driven gap analysis and ranking', 'complete' => true],
                        ['title' => 'Report quality feedback loop and scoring', 'complete' => true],
                        ['title' => 'Report write-back into Laravel', 'complete' => true],
                        ['title' => 'On-site inbox and scan-ready emails', 'complete' => true],
                        ['title' => 'Worker health and incident monitoring', 'complete' => true],
                    ],
                ],
            ],
        ]);
    }
}
