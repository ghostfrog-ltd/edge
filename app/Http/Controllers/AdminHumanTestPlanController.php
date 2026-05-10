<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AdminHumanTestPlanController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.test-plan', [
            'phases' => [
                [
                    'title' => 'Public journey',
                    'goal' => 'Check the anonymous experience before sign-up feels trustworthy and clear.',
                    'checks' => [
                        'Visit the homepage, pricing, how it works, terms, privacy, and help pages in both light and dark mode.',
                        'Confirm primary CTAs go to the right destinations and the Create Account button appears for logged-out visitors.',
                        'Check footer links, favicon, meta titles, and the support form are all present and readable.',
                    ],
                    'expected' => 'Public pages feel coherent, links work, and the product proposition is understandable before registration.',
                ],
                [
                    'title' => 'Account and team setup',
                    'goal' => 'Make sure a new customer can create an account and land in a usable workspace.',
                    'checks' => [
                        'Register a new account, verify login, and confirm the default personal team is created.',
                        'Open profile settings, team settings, and switch around the main app pages.',
                        'Confirm the new workspace starts with the expected credit balance.',
                    ],
                    'expected' => 'A new user can sign up cleanly, access the app, and understand the workspace context immediately.',
                ],
                [
                    'title' => 'Billing and credits',
                    'goal' => 'Validate the paid flow and prove credits move correctly after Stripe events.',
                    'checks' => [
                        'Open the billing page, verify the plan cards and top-up offers match the admin pricing catalog.',
                        'Run a subscription checkout and a top-up checkout in Stripe test mode.',
                        'Confirm the billing page settles correctly after return, credits are granted once, and duplicate webhook events do not double-credit the team.',
                    ],
                    'expected' => 'Subscriptions and top-ups complete cleanly, webhook fulfillment works, and the credit ledger matches what happened in Stripe.',
                ],
                [
                    'title' => 'Scan to report loop',
                    'goal' => 'Test the core product journey from queueing a scan to reading the finished report.',
                    'checks' => [
                        'Queue a specific niche scan with optional category ID and competitor store URL.',
                        'Watch it move from queued to processing to completed, then review The Missing 3, schema audit, VoC insights, and live eBay evidence.',
                        'Retry a failed scan intentionally once and confirm the refund / re-reservation behavior is correct.',
                    ],
                    'expected' => 'The async pipeline feels reliable, the report is readable, and the customer never loses track of what happened to their credit.',
                ],
                [
                    'title' => 'Notifications and PDF delivery',
                    'goal' => 'Check the customer sees the result both inside the app and outside it.',
                    'checks' => [
                        'Confirm the inbox badge appears without a full refresh when a scan finishes.',
                        'Confirm scan-ready email arrives with the signed access link.',
                        'Download the PDF report and check that page 2 starts with The Missing 3 and the schema audit reads like prose, not JSON.',
                    ],
                    'expected' => 'Customers get a timely notification, the report is shareable, and the PDF feels presentable enough to send on.',
                ],
                [
                    'title' => 'Admin and monitoring',
                    'goal' => 'Make sure the operator can see what the system is doing and spot issues quickly.',
                    'checks' => [
                        'Open the admin dashboard, roadmap, users, plans, pricing sync, and support pages.',
                        'Check the worker health panel for engine reachability, queue backlog, callback failures, and recent quality alerts.',
                        'Review at least one low-confidence or not-helpful report and confirm the quality loop state is visible.',
                    ],
                    'expected' => 'The operator has enough visibility to monitor scans, spot incidents, and follow up on weak reports without touching the database.',
                ],
            ],
        ]);
    }
}
