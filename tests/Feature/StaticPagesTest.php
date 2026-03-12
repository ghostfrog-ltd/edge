<?php

namespace Tests\Feature;

use Tests\TestCase;

class StaticPagesTest extends TestCase
{
    public function test_terms_page_is_accessible(): void
    {
        $this->get(route('terms.show'))
            ->assertOk()
            ->assertSee('Terms and Conditions | Ghostfrog Ebay Edge', false);
    }

    public function test_privacy_page_is_accessible(): void
    {
        $this->get(route('policy.show'))
            ->assertOk()
            ->assertSee('Privacy Policy | Ghostfrog Ebay Edge', false);
    }

    public function test_contact_page_is_accessible(): void
    {
        $this->get(route('contact.show'))
            ->assertOk()
            ->assertSee('Get in touch')
            ->assertSee('Contact Ghostfrog Ebay Edge', false)
            ->assertSee('billing questions', false);
    }

    public function test_sitemap_robots_and_llms_files_are_accessible(): void
    {
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('content-type', 'application/xml; charset=UTF-8')
            ->assertSee(route('home'), false)
            ->assertSee(route('how-it-works'), false);

        $this->get(route('robots'))
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=UTF-8')
            ->assertSee('Sitemap: '.route('sitemap'));

        $this->get(route('llms'))
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=UTF-8')
            ->assertSee('Ghostfrog Ebay Edge')
            ->assertSee(route('how-it-works'), false);
    }
}
