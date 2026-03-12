<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class BriefController extends Controller
{
    public function __invoke(): View
    {
        return view('brief', [
            'brief' => [
                'mission' => 'Build a reusable SaaS factory where Laravel handles the business chassis and Python handles the agentic analysis.',
                'product' => 'Ghostfrog Ebay Edge is the first product: an eBay niche gap finder that shows sellers the missing attributes, weak competitor signals, and next listing moves.',
                'businessModel' => 'Hybrid pricing with a monthly subscription, team access, and scan credits that are reserved at queue time and finalized after successful analysis.',
                'pythonBrain' => 'The Python brain takes a queued niche or keyword scan, searches eBay for relevant listings, gathers competitor evidence, compares strong and weak listing attributes, identifies missing fields and opportunities, and writes back a structured report for Laravel to show. The current input is a keyword or niche search, not a seller username scan.',
                'pythonBrainHow' => 'The brain is likely a mix of normal Python code and LLM reasoning. Regular code fetches listings, cleans the data, groups attributes, and compares competitors. The LLM then looks at that structured evidence and decides which gaps matter most, what patterns are important, and what actions the seller should take next.',
                'creditValue' => 'One credit is used for one successful scan. In return, the user gets a gap report with the missing attributes to add, competitor weaknesses worth exploiting, and practical listing actions to try next.',
            ],
            'pillars' => [
                [
                    'title' => 'Laravel chassis',
                    'description' => 'Auth, teams, credits, billing, dashboards, scan intake, and report delivery all live here.',
                ],
                [
                    'title' => 'Python brain',
                    'description' => 'FastAPI, LangGraph orchestration, and long-running analysis workers process the actual market intelligence and turn queued scans into report data.',
                ],
                [
                    'title' => 'Low-liability positioning',
                    'description' => 'We are selling market intelligence, not compliance advice, which keeps the product safer and clearer.',
                ],
            ],
            'tasks' => [
                [
                    'title' => 'Laravel app scaffolded and running in Herd',
                    'detail' => 'Project bootstrapped, HTTPS domain fixed, dependencies installed, and tests/builds passing.',
                    'complete' => true,
                ],
                [
                    'title' => 'Jetstream auth with Teams enabled',
                    'detail' => 'Users can register, manage profiles, create teams, invite members, and switch workspaces.',
                    'complete' => true,
                ],
                [
                    'title' => 'Credit ledger and starter balance',
                    'detail' => 'Each team gets starter credits and scan reservations are tracked as ledger events.',
                    'complete' => true,
                ],
                [
                    'title' => 'Scan intake workflow',
                    'detail' => 'Users can queue scans from the UI, attach them to the current team, and view scan history.',
                    'complete' => true,
                ],
                [
                    'title' => 'Stripe billing and top-ups',
                    'detail' => 'Add subscriptions, top-up packs, and a proper billing screen for real credit purchases.',
                    'complete' => false,
                ],
                [
                    'title' => 'Public pricing page',
                    'detail' => 'Explain subscription tiers, included scans, and credit top-up options before sign-up.',
                    'complete' => false,
                ],
                [
                    'title' => 'Help and support page',
                    'detail' => 'Give customers one place for FAQs, support guidance, and simple product explanations.',
                    'complete' => false,
                ],
                [
                    'title' => 'Python brain pipeline',
                    'detail' => 'Build the full analysis pipeline that picks up queued scans, reasons over marketplace evidence, and returns a real report to Laravel.',
                    'complete' => false,
                    'subtasks' => [
                        [
                            'title' => 'FastAPI bridge and callback contract',
                            'complete' => false,
                        ],
                        [
                            'title' => 'Queue worker and scan handoff',
                            'complete' => false,
                        ],
                        [
                            'title' => 'LLM-driven gap analysis and ranking',
                            'complete' => false,
                        ],
                        [
                            'title' => 'First report write-back into Laravel',
                            'complete' => false,
                        ],
                        [
                            'title' => 'Worker health and monitoring',
                            'complete' => false,
                        ],
                        [
                            'title' => 'Customer notification when a scan is ready',
                            'complete' => false,
                        ],
                    ],
                ],
            ],
        ]);
    }
}
