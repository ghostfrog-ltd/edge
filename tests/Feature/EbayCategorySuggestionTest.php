<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EbayCategorySuggestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_fetch_ebay_category_suggestions(): void
    {
        Cache::flush();

        config()->set('services.ebay.client_id', 'test-client-id');
        config()->set('services.ebay.client_secret', 'test-client-secret');

        Http::fake([
            'https://api.ebay.com/identity/v1/oauth2/token' => Http::response([
                'access_token' => 'test-access-token',
                'expires_in' => 7200,
            ]),
            'https://api.ebay.com/commerce/taxonomy/v1/get_default_category_tree_id*' => Http::response([
                'categoryTreeId' => '3',
            ]),
            'https://api.ebay.com/commerce/taxonomy/v1/category_tree/3/get_category_suggestions*' => Http::response([
                'categorySuggestions' => [
                    [
                        'category' => [
                            'categoryId' => '179753',
                            'categoryName' => 'Motorcycle Parts',
                        ],
                    ],
                ],
            ]),
        ]);

        $user = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        $user->forceFill(['current_team_id' => $team->id])->save();

        $this->actingAs($user)
            ->getJson(route('scans.ebay-category-suggestions', [
                'keyword' => 'Honda PCX 125',
                'marketplace' => 'ebay-uk',
            ]))
            ->assertOk()
            ->assertJsonPath('suggestions.0.id', '179753')
            ->assertJsonPath('suggestions.0.name', 'Motorcycle Parts')
            ->assertJsonPath('suggestions.0.label', 'Motorcycle Parts (179753)');
    }
}
