<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AdminPlanController extends Controller
{
    public function index(): View
    {
        return view('admin.plans.index', [
            'plans' => [
                [
                    'name' => 'Free',
                    'status' => 'planned',
                    'price' => 'PS0',
                    'credits' => 'Small trial allowance',
                    'audience' => 'New sellers trying the workflow before paying.',
                    'summary' => 'Very limited scans with a simple report output so users can understand the value before upgrading.',
                ],
                [
                    'name' => 'Starter',
                    'status' => 'likely-first-paid',
                    'price' => 'PS29/mo',
                    'credits' => 'Monthly credit bundle',
                    'audience' => 'Solo sellers who want regular scan usage without team complexity.',
                    'summary' => 'Core Ghostfrog workflow with enough monthly credits to run niche research consistently.',
                ],
                [
                    'name' => 'Pro',
                    'status' => 'planned',
                    'price' => 'PS79/mo',
                    'credits' => 'Larger monthly credit bundle',
                    'audience' => 'Power sellers who want deeper usage, faster throughput, and better reporting depth.',
                    'summary' => 'Higher limits, richer reporting, and priority treatment once the scan pipeline matures.',
                ],
                [
                    'name' => 'Team',
                    'status' => 'planned',
                    'price' => 'PS199/mo',
                    'credits' => 'Shared pooled credits',
                    'audience' => 'Agencies or shared ecommerce teams managing multiple operators in one workspace.',
                    'summary' => 'Collaborative workspace plan with shared credits, more members, and support for higher-volume use.',
                ],
            ],
        ]);
    }
}
