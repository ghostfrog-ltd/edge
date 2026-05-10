<?php

namespace Tests\Feature;

use App\Models\Scan;
use App\Models\ScanEvidenceListing;
use App\Models\ScanReport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScanPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_can_download_a_completed_scan_pdf(): void
    {
        [$user, $team] = $this->createUserWithTeam();

        $scan = Scan::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'scan_type' => 'keyword',
            'keyword' => 'Refurbished Apple iPad Pro 12.9 5th Gen',
            'marketplace' => 'ebay-uk',
            'status' => 'completed',
            'reserved_credits' => 1,
            'completed_at' => now(),
        ]);

        ScanReport::create([
            'scan_id' => $scan->id,
            'summary' => 'This scan found the strongest edge in clearer iPad generation and charger disclosure.',
            'missing_three' => [
                [
                    'title' => 'Battery cycle count',
                    'why_it_matters' => 'Buyers use it to gauge longevity.',
                    'what_to_add' => 'Add the cycle count in item specifics and the first paragraph.',
                    'evidence_source' => 'Repeated across top live listings and buyer-friction signals.',
                ],
            ],
            'missing_attributes' => ['Battery cycle count'],
            'schema_audit' => ['Original charger type is underused in item specifics.'],
            'voc_insights' => ['Buyers keep asking whether the charger is original.'],
            'competitor_insights' => ['Top sellers lead with condition clarity.'],
            'listing_actions' => ['Add a charger brand line near the title and specifics.'],
            'generated_at' => now(),
        ]);

        ScanEvidenceListing::create([
            'scan_id' => $scan->id,
            'rank' => 1,
            'title' => 'Apple iPad Pro 12.9 5th Gen 128GB Wi-Fi',
            'price_currency' => 'GBP',
            'price_value' => 499.99,
            'condition' => 'Used',
            'seller_username' => 'top-ipad-seller',
            'category_id' => '171485',
        ]);

        $response = $this->actingAs($user)->get(route('scans.pdf', $scan));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertHeader('content-disposition');
    }

    public function test_user_cannot_download_pdf_for_another_team_scan(): void
    {
        [$owner, $ownerTeam] = $this->createUserWithTeam();
        [$otherUser] = $this->createUserWithTeam();

        $scan = Scan::create([
            'team_id' => $ownerTeam->id,
            'user_id' => $owner->id,
            'scan_type' => 'keyword',
            'keyword' => 'Honda PCX 125 Spares/Repair',
            'marketplace' => 'ebay-uk',
            'status' => 'completed',
            'reserved_credits' => 1,
            'completed_at' => now(),
        ]);

        ScanReport::create([
            'scan_id' => $scan->id,
            'summary' => 'A stored report.',
            'generated_at' => now(),
        ]);

        $this->actingAs($otherUser)
            ->get(route('scans.pdf', $scan))
            ->assertNotFound();
    }

    public function test_user_cannot_download_pdf_before_scan_completion(): void
    {
        [$user, $team] = $this->createUserWithTeam();

        $scan = Scan::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'scan_type' => 'keyword',
            'keyword' => 'Vintage leather watch straps',
            'marketplace' => 'ebay-uk',
            'status' => 'processing',
            'reserved_credits' => 1,
        ]);

        $this->actingAs($user)
            ->get(route('scans.pdf', $scan))
            ->assertNotFound();
    }

    public function test_scan_report_formats_schema_audit_for_human_readable_pdf_output(): void
    {
        $report = new ScanReport([
            'schema_audit' => [[
                'aspect_name' => 'Battery Cycle Count',
                'requirement_level' => 'recommended',
                'headline' => 'Battery Cycle Count is recommended by eBay, but only 20% of the top 10 listings currently expose it.',
                'present_count' => 2,
                'checked_listing_count' => 10,
                'coverage_percent' => 20,
                'expected_required_by' => '2026-05-01',
            ]],
        ]);

        $formatted = $report->formattedSchemaAudit();

        $this->assertSame('Battery Cycle Count', $formatted[0]['title']);
        $this->assertStringContainsString('recommended by eBay', $formatted[0]['summary']);
        $this->assertStringContainsString('Present in 2 of 10 sampled listings', $formatted[0]['detail']);
        $this->assertStringContainsString('Expected to tighten by 2026-05-01', $formatted[0]['detail']);
    }

    protected function createUserWithTeam(): array
    {
        $user = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        $user->forceFill(['current_team_id' => $team->id])->save();

        return [$user, $team];
    }
}
