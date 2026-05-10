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
            'concretePlan' => [
                'intro' => "To finalize the product's core loop, this is the exact definition of what the user provides and what the system returns.",
                'inputs' => [
                    [
                        'title' => 'Keyword/Niche',
                        'detail' => 'A specific product or category they want to compete in.',
                        'example' => 'Vintage Leather Watch Straps',
                    ],
                    [
                        'title' => 'eBay Category ID',
                        'detail' => 'A numerical eBay category for a specific department.',
                        'example' => 'Category 179753 for Motorcycle Parts',
                    ],
                    [
                        'title' => 'Competitor Store URL',
                        'detail' => 'A direct link to a rival storefront to see what they are missing.',
                        'example' => 'https://www.ebay.co.uk/str/rival-parts-store',
                    ],
                ],
                'outputs' => [
                    [
                        'title' => 'The Missing 3',
                        'detail' => 'Three high-impact, actionable things to add to the listing based on the live evidence, schema audit, and buyer-friction signals.',
                    ],
                    [
                        'title' => 'Schema audit',
                        'detail' => 'A list of specific Item Specific fields that eBay recommends or requires but top listings are still underusing.',
                    ],
                    [
                    'title' => 'Voice of the Customer insights',
                    'detail' => 'Pain points extracted from competitor descriptions and buyer-friction signals where shoppers keep needing reassurance or extra clarity.',
                ],
                    [
                        'title' => 'Actionable implementation',
                        'detail' => 'A clear how-to for the user to update their own listings and fill those gaps.',
                    ],
                    [
                        'title' => 'Downloadable PDF report',
                        'detail' => 'When a scan completes, the user should be able to download the report as a clean PDF for sharing, saving, or client delivery.',
                    ],
                ],
                'examples' => [
                    [
                        'title' => 'Niche e-commerce',
                        'input' => 'Handmade Wooden Educational Toys',
                        'edge' => [
                            'Missing field: CE/UKCA safety certification number. Top sellers are only mentioning it in text, so adding the dedicated field creates a trust and compliance edge.',
                            'Buyer demand: Real-world scale photos. Reviews show buyers are often surprised by the small size, so adding a photo with a reference object should lower return risk.',
                            'Technical gap: Material origin. Naming the exact wood type such as beech versus pine helps appeal to eco-conscious buyers.',
                        ],
                    ],
                    [
                        'title' => 'High-value electronics',
                        'input' => 'Refurbished Apple iPad Pro 12.9 5th Gen',
                        'edge' => [
                            'Missing field: Battery cycle count. Sellers often show battery health percentage, but buyers keep asking for cycle count to judge longevity.',
                            'Buyer demand: Screen uniformity photos. Reviews show concern about white spots or bleed, so a pure white background photo gives an immediate trust edge.',
                            'Technical gap: Original versus third-party charger. State the charger brand in a dedicated attribute instead of burying it in the description.',
                        ],
                    ],
                    [
                        'title' => 'Automotive parts',
                        'input' => 'Honda PCX 125 Spares/Repair',
                        'edge' => [
                            'Missing field: Donor bike mileage. Buyers want a proxy for wear before purchasing internal or mechanical parts.',
                            'Buyer demand: Close-up photos of spline or teeth wear. Complaints about rounded parts make macro shots a strong trust differentiator.',
                            'Technical gap: Compatibility years. Use the latest eBay schema to list every fitment year instead of saying it fits most models.',
                        ],
                    ],
                ],
                'boundary' => 'This is the product: pure, high-value intelligence on how to win the eBay listing race. No ROI tracking and no inventory management.',
            ],
            'brainSteps' => [
                [
                    'title' => 'Step 1: Live listing evidence collection',
                    'detail' => 'The brain fetches the first 50 relevant active eBay listings for the keyword or niche, constrained by marketplace and sharpened by the optional category ID and competitor store URL when provided.',
                ],
                [
                    'title' => 'Step 2: Schema audit',
                    'detail' => 'It pulls eBay taxonomy aspect guidance for the category and checks listing details to find required or recommended item specifics that top sellers are still underusing.',
                ],
                [
                    'title' => 'Step 3: Intelligence gathering',
                    'detail' => 'It reads competitor descriptions and buyer-friction signals to surface the reassurance points and unanswered questions buyers keep circling around.',
                ],
                [
                    'title' => 'Step 4: The Gap Analysis',
                    'detail' => 'It synthesizes the live evidence, schema audit, and buyer-friction signals into The Missing 3: three specific things to add to the listing in order to beat the current top sellers.',
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
                    'detail' => 'Stripe-backed team subscriptions, top-up packs, webhook-based credit fulfillment, and a billing screen are now wired into the workspace flow.',
                    'complete' => true,
                ],
                [
                    'title' => 'Public pricing page',
                    'detail' => 'Explain subscription tiers, included scans, and credit top-up options before sign-up.',
                    'complete' => true,
                ],
                [
                    'title' => 'Help and support page',
                    'detail' => 'Give customers one place for FAQs, support guidance, and simple product explanations.',
                    'complete' => true,
                ],
                [
                    'title' => 'Downloadable PDF reports',
                    'detail' => 'Let customers export completed scan reports as a polished PDF they can save, share, or send to clients.',
                    'complete' => true,
                ],
                [
                    'title' => 'Python brain pipeline',
                    'detail' => 'The Ghostfrog engine now runs the v1 loop end to end: live eBay evidence, schema audit, buyer-friction intelligence, The Missing 3 synthesis, LLM-backed ranking, notifications, PDF delivery, and worker monitoring.',
                    'complete' => true,
                    'subtasks' => [
                        [
                            'title' => 'FastAPI bridge and callback contract',
                            'complete' => true,
                        ],
                        [
                            'title' => 'Queue worker and scan handoff',
                            'complete' => true,
                        ],
                        [
                            'title' => 'Live eBay evidence collection for the top 50 listings',
                            'complete' => true,
                        ],
                        [
                            'title' => 'Schema audit against eBay item specifics guidance',
                            'complete' => true,
                        ],
                        [
                            'title' => 'Description-driven buyer-friction intelligence gathering',
                            'complete' => true,
                        ],
                        [
                            'title' => 'The Missing 3 synthesis layer',
                            'complete' => true,
                        ],
                        [
                            'title' => 'LLM-driven gap analysis and ranking',
                            'complete' => true,
                        ],
                        [
                            'title' => 'Report quality feedback loop and scoring',
                            'complete' => true,
                        ],
                        [
                            'title' => 'First report write-back into Laravel',
                            'complete' => true,
                        ],
                        [
                            'title' => 'Worker health and monitoring',
                            'complete' => true,
                        ],
                        [
                            'title' => 'Customer inbox notification when a scan is ready',
                            'complete' => true,
                        ],
                        [
                            'title' => 'Customer email notification when a scan is ready',
                            'complete' => true,
                        ],
                    ],
                ],
                [
                    'title' => 'Human end-to-end system test plan',
                    'detail' => 'Before calling the product done, run a proper human test pass across sign-up, billing, scans, reports, inbox, email, PDF export, and admin flows so the whole system is checked outside of automated tests.',
                    'complete' => true,
                ],
            ],
        ]);
    }
}
