<?php

namespace Tests\Feature;

use App\Jobs\DispatchScanToEngine;
use App\Mail\ScanCompletedMail;
use App\Mail\ScanFailedMail;
use App\Models\CreditLedger;
use App\Models\InboxNotification;
use App\Models\Scan;
use App\Models\ScanEvidenceListing;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ScanEngineBridgeTest extends TestCase
{
    use RefreshDatabase;

    protected function makeUserWithTeamAndCredits(): array
    {
        $user = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => true,
            'name' => 'Bridge Team',
        ]);

        $user->forceFill(['current_team_id' => $team->id])->save();

        CreditLedger::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'type' => 'starter_grant',
            'amount' => 50,
            'description' => 'Starter credits for engine bridge testing.',
        ]);

        return [$user, $team];
    }

    public function test_scan_submission_dispatches_the_engine_job(): void
    {
        Queue::fake();

        [$user] = $this->makeUserWithTeamAndCredits();

        $this->actingAs($user)->post(route('scans.store'), [
            'keyword' => 'lego castle byers',
            'marketplace' => 'ebay-uk',
        ])->assertRedirect();

        Queue::assertPushed(DispatchScanToEngine::class);
    }

    public function test_dispatch_job_posts_scan_to_engine_and_marks_it_processing(): void
    {
        [$user, $team] = $this->makeUserWithTeamAndCredits();

        config()->set('services.ebay.client_id', 'test-client-id');
        config()->set('services.ebay.client_secret', 'test-client-secret');

        $scan = Scan::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'scan_type' => 'keyword',
            'keyword' => 'omega seamaster dial',
            'marketplace' => 'ebay-uk',
            'ebay_category_id' => '12345',
            'competitor_store_url' => 'https://www.ebay.co.uk/str/example-store',
            'status' => 'queued',
            'reserved_credits' => 1,
            'queued_at' => now(),
        ]);

        Http::fake([
            'https://api.ebay.com/identity/v1/oauth2/token' => Http::response([
                'access_token' => 'test-access-token',
                'expires_in' => 7200,
            ]),
            'https://api.ebay.com/commerce/taxonomy/v1/get_default_category_tree_id*' => Http::response([
                'categoryTreeId' => '3',
            ]),
            'https://api.ebay.com/commerce/taxonomy/v1/category_tree/3/get_item_aspects_for_category*' => Http::response([
                'aspects' => [
                    [
                        'localizedAspectName' => 'Battery Cycle Count',
                        'aspectConstraint' => [
                            'aspectUsage' => 'RECOMMENDED',
                            'aspectRequired' => false,
                        ],
                    ],
                    [
                        'localizedAspectName' => 'Screen Size',
                        'aspectConstraint' => [
                            'aspectUsage' => 'RECOMMENDED',
                            'aspectRequired' => false,
                        ],
                    ],
                ],
            ]),
            'https://api.ebay.com/buy/browse/v1/item_summary/search*' => Http::response([
                'itemSummaries' => [
                    [
                        'itemId' => 'v1|123|0',
                        'title' => 'Omega Seamaster Dial Genuine Part',
                        'price' => ['value' => '49.99', 'currency' => 'GBP'],
                        'condition' => 'Used',
                        'itemWebUrl' => 'https://www.ebay.co.uk/itm/123',
                        'image' => ['imageUrl' => 'https://i.ebayimg.com/images/g/example/s-l1600.jpg'],
                        'seller' => [
                            'username' => 'example-store',
                            'feedbackPercentage' => '99.8',
                            'feedbackScore' => 5000,
                        ],
                        'buyingOptions' => ['FIXED_PRICE'],
                        'leafCategoryIds' => ['12345'],
                    ],
                    [
                        'itemId' => 'v1|124|0',
                        'title' => 'Omega Seamaster Dial Original Charger Bundle',
                        'price' => ['value' => '59.99', 'currency' => 'GBP'],
                        'condition' => 'Used',
                        'itemWebUrl' => 'https://www.ebay.co.uk/itm/124',
                        'image' => ['imageUrl' => 'https://i.ebayimg.com/images/g/example-two/s-l1600.jpg'],
                        'seller' => [
                            'username' => 'example-store-2',
                            'feedbackPercentage' => '99.1',
                            'feedbackScore' => 3200,
                        ],
                        'buyingOptions' => ['FIXED_PRICE'],
                        'leafCategoryIds' => ['12345'],
                    ],
                ],
            ]),
            'https://api.ebay.com/buy/browse/v1/item/*' => Http::response([
                'title' => 'Omega Seamaster Dial Genuine Part',
                'description' => 'Battery cycle count not relevant. Original charger not included. Screen condition clear.',
                'localizedAspects' => [
                    [
                        'name' => 'Screen Size',
                        'values' => ['42 mm'],
                    ],
                ],
            ]),
            'http://127.0.0.1:8001/api/v1/scans/dispatch' => Http::response([
                'accepted' => true,
                'status' => 'processing',
                'engine_job_id' => 'gf-test-job-1',
            ]),
        ]);

        $this->app->make(DispatchScanToEngine::class, ['scanId' => $scan->id])->handle(
            $this->app->make(\App\Services\Engine\GhostfrogEngineClient::class),
            $this->app->make(\App\Services\Scans\ScanPipelineService::class),
            $this->app->make(\App\Services\Ebay\EbayListingEvidenceService::class),
            $this->app->make(\App\Services\Ebay\EbaySchemaAuditService::class),
            $this->app->make(\App\Services\Ebay\EbayIntelligenceGatheringService::class)
        );

        Http::assertSent(fn ($request) => $request->url() === 'http://127.0.0.1:8001/api/v1/scans/dispatch'
            && $request['scan_id'] === $scan->id
            && $request['scan_type'] === 'keyword'
            && $request['ebay_category_id'] === '12345'
            && $request['competitor_store_url'] === 'https://www.ebay.co.uk/str/example-store'
            && data_get($request['schema_audit'], '0.aspect_name') === 'Battery Cycle Count'
            && filled(data_get($request['intelligence_gathering'], '0.headline'))
            && $request['callback_url'] === route('engine.scans.callback', $scan));

        $this->assertDatabaseHas('scans', [
            'id' => $scan->id,
            'status' => 'processing',
            'engine_job_id' => 'gf-test-job-1',
        ]);

        $this->assertDatabaseHas('scan_evidence_listings', [
            'scan_id' => $scan->id,
            'rank' => 1,
            'title' => 'Omega Seamaster Dial Genuine Part',
            'seller_username' => 'example-store',
        ]);
    }

    public function test_dispatch_job_fails_cleanly_when_ebay_returns_no_active_listings(): void
    {
        [$user, $team] = $this->makeUserWithTeamAndCredits();

        $scan = Scan::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'scan_type' => 'keyword',
            'keyword' => 'honda nc750s',
            'marketplace' => 'ebay-uk',
            'ebay_category_id' => '12345',
            'competitor_store_url' => 'https://www.ebay.co.uk/str/example-store',
            'status' => 'queued',
            'reserved_credits' => 1,
            'queued_at' => now(),
        ]);

        Http::fake([
            'https://api.ebay.com/identity/v1/oauth2/token' => Http::response([
                'access_token' => 'test-access-token',
                'expires_in' => 7200,
            ]),
            'https://api.ebay.com/buy/browse/v1/item_summary/search*' => Http::response([
                'itemSummaries' => [],
            ]),
        ]);

        $this->app->make(DispatchScanToEngine::class, ['scanId' => $scan->id])->handle(
            $this->app->make(\App\Services\Engine\GhostfrogEngineClient::class),
            $this->app->make(\App\Services\Scans\ScanPipelineService::class),
            $this->app->make(\App\Services\Ebay\EbayListingEvidenceService::class),
            $this->app->make(\App\Services\Ebay\EbaySchemaAuditService::class),
            $this->app->make(\App\Services\Ebay\EbayIntelligenceGatheringService::class)
        );

        Http::assertNotSent(fn ($request) => $request->url() === 'http://127.0.0.1:8001/api/v1/scans/dispatch');

        $this->assertDatabaseHas('scans', [
            'id' => $scan->id,
            'status' => 'failed',
        ]);

        $scan->refresh();

        $this->assertStringContainsString('could not find enough active eBay listings', (string) $scan->failure_reason);
        $this->assertStringNotContainsString('Engine dispatch failed after retries', (string) $scan->failure_reason);

        $this->assertDatabaseHas('credit_ledgers', [
            'team_id' => $team->id,
            'type' => 'scan_refund',
            'amount' => 1,
        ]);
    }

    public function test_engine_callback_completes_scan_and_writes_report(): void
    {
        Mail::fake();

        [$user, $team] = $this->makeUserWithTeamAndCredits();

        $scan = Scan::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'scan_type' => 'keyword',
            'keyword' => 'vintage star wars figure',
            'marketplace' => 'ebay-uk',
            'status' => 'processing',
            'engine_job_id' => 'gf-callback-job',
            'reserved_credits' => 1,
            'queued_at' => now(),
            'engine_dispatched_at' => now(),
            'processing_started_at' => now(),
        ]);

        $this->postJson(route('engine.scans.callback', $scan), [
            'status' => 'completed',
            'engine_job_id' => 'gf-callback-job',
            'summary' => 'A structured report has been generated.',
            'missing_three' => [
                [
                    'title' => 'Battery Cycle Count',
                    'why_it_matters' => 'Battery cycle count keeps showing up in competitor detail text, which suggests buyers are actively looking for a stronger longevity signal.',
                    'what_to_add' => 'Add battery cycle count high in the listing so buyers do not have to ask.',
                    'evidence_source' => 'Schema audit',
                ],
                [
                    'title' => 'Original Charger Status',
                    'why_it_matters' => 'Charger inclusion and originality are recurring reassurance points in competitor descriptions.',
                    'what_to_add' => 'State whether the charger is original, third-party, or missing.',
                    'evidence_source' => 'VoC intelligence',
                ],
                [
                    'title' => 'Battery Health Percentage',
                    'why_it_matters' => 'This still shows up as a weak spot across the strongest listings on eBay.',
                    'what_to_add' => 'Make battery health percentage explicit in the title, item specifics, and opening description block.',
                    'evidence_source' => 'Gap analysis',
                ],
            ],
            'missing_attributes' => ['Condition', 'Completeness'],
            'schema_audit' => [
                [
                    'aspect_name' => 'Battery Cycle Count',
                    'requirement_level' => 'recommended',
                    'headline' => 'Battery Cycle Count is recommended by eBay, but only 10% of the top 10 listings currently expose it.',
                    'present_count' => 1,
                    'missing_count' => 9,
                    'checked_listing_count' => 10,
                    'coverage_percent' => 10,
                    'expected_required_by' => null,
                ],
            ],
            'voc_insights' => [
                'Battery cycle count keeps showing up in competitor detail text, which suggests buyers are actively looking for a stronger longevity signal.',
            ],
            'competitor_insights' => ['Top listings mention completeness early.'],
            'listing_actions' => ['Rewrite the title.'],
            'report_meta' => [
                'llm_provider' => 'Gemini:gemini-2.5-flash',
                'ranking_provider' => 'Gemini:gemini-2.5-flash',
                'ranking_status' => 'gemini',
                'ranking_rationale' => 'The strongest schema and buyer-friction gaps were ranked first.',
                'confidence_score' => 84,
                'quality_score' => 78,
            ],
        ], [
            'X-Ghostfrog-Callback-Secret' => 'ghostfrog-callback-secret',
        ])->assertOk();

        $this->assertDatabaseHas('scans', [
            'id' => $scan->id,
            'status' => 'completed',
            'consumed_credits' => 1,
        ]);

        $this->assertDatabaseHas('scan_reports', [
            'scan_id' => $scan->id,
            'summary' => 'A structured report has been generated.',
        ]);

        $report = $scan->fresh('report')->report;

        $this->assertSame(0, (int) data_get($report->report_meta, 'evidence_count', -1));
        $this->assertSame(84, (int) data_get($report->report_meta, 'confidence_score'));
        $this->assertSame(78, (int) data_get($report->report_meta, 'quality_score'));
        $this->assertSame('awaiting_customer_feedback', data_get($report->report_meta, 'feedback_loop_state'));
        $this->assertSame('Battery Cycle Count', data_get($report->missing_three, '0.title'));
        $this->assertSame('Battery Cycle Count', data_get($report->schema_audit, '0.aspect_name'));
        $this->assertStringContainsString('Battery cycle count', data_get($report->voc_insights, '0'));

        $this->assertDatabaseHas('inbox_notifications', [
            'user_id' => $user->id,
            'team_id' => $team->id,
            'type' => 'scan_completed',
            'title' => 'Scan ready: '.$scan->keyword,
        ]);

        Mail::assertSent(ScanCompletedMail::class, function (ScanCompletedMail $mail) use ($scan, $user) {
            return $mail->hasTo($user->email) && $mail->scan->is($scan);
        });

        $rendered = (new ScanCompletedMail($scan->fresh('report')))->render();

        $this->assertStringContainsString('/email/scan-access/'.$scan->id, $rendered);
        $this->assertStringNotContainsString('href="'.route('scans.show', $scan).'"', $rendered);
    }

    public function test_engine_callback_failure_refunds_the_reserved_credit(): void
    {
        Mail::fake();

        [$user, $team] = $this->makeUserWithTeamAndCredits();

        $scan = Scan::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'scan_type' => 'keyword',
            'keyword' => 'fail this scan',
            'marketplace' => 'ebay-uk',
            'status' => 'processing',
            'engine_job_id' => 'gf-failed-job',
            'reserved_credits' => 1,
            'queued_at' => now(),
            'engine_dispatched_at' => now(),
            'processing_started_at' => now(),
        ]);

        $this->postJson(route('engine.scans.callback', $scan), [
            'status' => 'failed',
            'engine_job_id' => 'gf-failed-job',
            'failure_reason' => 'The engine could not fetch enough evidence.',
        ], [
            'X-Ghostfrog-Callback-Secret' => 'ghostfrog-callback-secret',
        ])->assertOk();

        $this->assertDatabaseHas('scans', [
            'id' => $scan->id,
            'status' => 'failed',
        ]);

        $this->assertDatabaseHas('credit_ledgers', [
            'team_id' => $team->id,
            'type' => 'scan_refund',
            'amount' => 1,
        ]);

        $this->assertDatabaseHas('inbox_notifications', [
            'user_id' => $user->id,
            'team_id' => $team->id,
            'type' => 'scan_failed',
            'title' => 'Scan failed: '.$scan->keyword,
        ]);

        Mail::assertSent(ScanFailedMail::class, function (ScanFailedMail $mail) use ($scan, $user) {
            return $mail->hasTo($user->email) && $mail->scan->is($scan);
        });
    }

    public function test_completed_report_can_store_feedback(): void
    {
        [$user, $team] = $this->makeUserWithTeamAndCredits();

        $scan = Scan::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'scan_type' => 'keyword',
            'keyword' => 'ipad feedback',
            'marketplace' => 'ebay-uk',
            'status' => 'completed',
            'reserved_credits' => 1,
            'consumed_credits' => 1,
            'queued_at' => now(),
            'completed_at' => now(),
        ]);

        $scan->report()->create([
            'summary' => 'Helpful report summary.',
            'missing_attributes' => ['Generation'],
            'competitor_insights' => ['Top eBay listings are clearer.'],
            'listing_actions' => ['Rewrite the title.'],
            'report_meta' => [
                'quality_score' => 72,
                'quality_loop_score' => 72,
                'feedback_loop_state' => 'awaiting_customer_feedback',
            ],
            'generated_at' => now(),
        ]);

        $this->actingAs($user)
            ->post(route('scans.feedback', $scan), [
                'feedback_rating' => 'helpful',
                'feedback_notes' => 'This felt much more eBay-specific.',
            ])
            ->assertRedirect(route('scans.show', $scan));

        $this->assertDatabaseHas('scan_reports', [
            'scan_id' => $scan->id,
            'feedback_rating' => 'helpful',
            'feedback_notes' => 'This felt much more eBay-specific.',
        ]);

        $report = $scan->fresh('report')->report;

        $this->assertSame('validated_by_customer', data_get($report->report_meta, 'feedback_loop_state'));
        $this->assertSame(80, (int) data_get($report->report_meta, 'quality_loop_score'));
    }

    public function test_signed_email_scan_link_requires_matching_logged_in_user(): void
    {
        [$user, $team] = $this->makeUserWithTeamAndCredits();
        $otherUser = User::factory()->create();
        $otherTeam = Team::factory()->create([
            'user_id' => $otherUser->id,
            'personal_team' => true,
        ]);
        $otherUser->forceFill(['current_team_id' => $otherTeam->id])->save();

        $scan = Scan::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'scan_type' => 'keyword',
            'keyword' => 'signed access',
            'marketplace' => 'ebay-uk',
            'status' => 'completed',
            'reserved_credits' => 1,
            'consumed_credits' => 1,
            'queued_at' => now(),
            'completed_at' => now(),
        ]);

        $signedUrl = URL::temporarySignedRoute('email.scans.access', now()->addHour(), ['scan' => $scan->id]);

        $this->actingAs($user)
            ->get($signedUrl)
            ->assertRedirect(route('scans.show', $scan));

        $this->actingAs($otherUser)
            ->get($signedUrl)
            ->assertForbidden();
    }

    public function test_failed_scan_can_be_retried_from_the_website(): void
    {
        Queue::fake();

        [$user, $team] = $this->makeUserWithTeamAndCredits();

        $scan = Scan::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'scan_type' => 'keyword',
            'keyword' => 'ipad retry',
            'marketplace' => 'ebay-us',
            'status' => 'failed',
            'reserved_credits' => 1,
            'queued_at' => now()->subMinutes(5),
            'failed_at' => now()->subMinute(),
            'failure_reason' => 'Engine dispatch failed after retries.',
        ]);

        $scan->report()->create([
            'summary' => 'Old failed report.',
            'missing_attributes' => ['Old attribute'],
            'competitor_insights' => ['Old insight'],
            'listing_actions' => ['Old action'],
            'generated_at' => now()->subMinutes(5),
        ]);

        $scan->evidenceListings()->create([
            'rank' => 1,
            'ebay_item_id' => 'v1|old|0',
            'title' => 'Old evidence listing',
            'condition' => 'Used',
            'price_value' => 10,
            'price_currency' => 'GBP',
            'item_web_url' => 'https://www.ebay.co.uk/itm/old',
            'image_url' => 'https://i.ebayimg.com/images/g/old/s-l1600.jpg',
            'seller_username' => 'old-seller',
            'seller_feedback_percentage' => 99.5,
            'seller_feedback_score' => 10,
            'shipping_summary' => 'FREE',
            'buying_options' => ['FIXED_PRICE'],
            'category_id' => '123',
            'category_name' => 'Tablets',
            'raw_payload' => ['id' => 'old'],
        ]);

        $this->actingAs($user)
            ->post(route('scans.retry', $scan))
            ->assertRedirect(route('scans.submitted', $scan));

        $scan->refresh();

        $this->assertDatabaseHas('scans', [
            'id' => $scan->id,
            'status' => 'queued',
            'failure_reason' => null,
            'engine_job_id' => null,
        ]);
        $this->assertTrue($scan->queued_at?->greaterThan(now()->subMinute()));
        $this->assertDatabaseMissing('scan_reports', [
            'scan_id' => $scan->id,
        ]);
        $this->assertDatabaseMissing('scan_evidence_listings', [
            'scan_id' => $scan->id,
        ]);

        $this->assertDatabaseHas('credit_ledgers', [
            'team_id' => $team->id,
            'type' => 'scan_reservation',
            'amount' => -1,
            'description' => 'Reserved 1 credit for retry of scan #'.$scan->id.'.',
        ]);

        Queue::assertPushed(DispatchScanToEngine::class);
    }

    public function test_completed_scan_detail_page_shows_live_ebay_evidence(): void
    {
        [$user, $team] = $this->makeUserWithTeamAndCredits();

        $scan = Scan::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'scan_type' => 'keyword',
            'keyword' => 'Apple iPad Pro 12.9',
            'marketplace' => 'ebay-uk',
            'status' => 'completed',
            'reserved_credits' => 1,
            'consumed_credits' => 1,
            'queued_at' => now(),
            'completed_at' => now(),
        ]);

        $scan->report()->create([
            'summary' => 'Report ready.',
            'missing_three' => [
                [
                    'title' => 'Battery Cycle Count',
                    'why_it_matters' => 'Battery cycle count keeps showing up in competitor detail text, which suggests buyers are actively looking for a stronger longevity signal.',
                    'what_to_add' => 'Add battery cycle count high in the listing so buyers do not have to ask.',
                    'evidence_source' => 'Schema audit',
                ],
            ],
            'missing_attributes' => ['Generation'],
            'schema_audit' => [
                [
                    'aspect_name' => 'Battery Cycle Count',
                    'requirement_level' => 'recommended',
                    'headline' => 'Battery Cycle Count is recommended by eBay, but only 10% of the top listings currently expose it.',
                ],
            ],
            'voc_insights' => [
                'Battery cycle count keeps showing up in competitor detail text, which suggests buyers are actively looking for a stronger longevity signal.',
            ],
            'competitor_insights' => ['Top listings clarify storage.'],
            'listing_actions' => ['Rewrite the title.'],
            'generated_at' => now(),
        ]);

        ScanEvidenceListing::create([
            'scan_id' => $scan->id,
            'rank' => 1,
            'ebay_item_id' => 'v1|100|0',
            'title' => 'Apple iPad Pro 12.9 5th Gen 128GB Wi-Fi',
            'condition' => 'Used',
            'price_value' => 599.99,
            'price_currency' => 'GBP',
            'item_web_url' => 'https://www.ebay.co.uk/itm/100',
            'seller_username' => 'ipad-pro-seller',
            'buying_options' => ['FIXED_PRICE'],
        ]);

        $this->actingAs($user)
            ->get(route('scans.show', $scan))
            ->assertOk()
            ->assertSee('Live eBay evidence')
            ->assertSee('The Missing 3')
            ->assertSee('Schema audit')
            ->assertSee('VoC insights')
            ->assertSee('Battery Cycle Count')
            ->assertSee('Apple iPad Pro 12.9 5th Gen 128GB Wi-Fi')
            ->assertSee('Open listing');
    }
}
