<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BriefPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_the_how_it_works_page(): void
    {
        $response = $this->get(route('how-it-works'));

        $response->assertOk();
        $response->assertSee('How Ghostfrog works');
        $response->assertSee('Tasks and delivery status');
        $response->assertSee('Complete');
        $response->assertSee('Pending');
        $response->assertSee('How Ghostfrog Works | Ghostfrog Ebay Edge', false);
        $response->assertSee('What one credit buys');
    }
}
