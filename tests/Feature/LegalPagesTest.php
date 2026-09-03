<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_footer_links_to_all_legal_placeholder_pages(): void
    {
        $this->withSession(['gate_unlocked' => true]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Impressum')
            ->assertSee('Datenschutz')
            ->assertSee('AGB')
            ->assertSee('Kontakt');
    }

    public function test_impressum_placeholder_page_is_reachable(): void
    {
        $this->get('/impressum')->assertOk()->assertSee('Impressum');
    }

    public function test_datenschutz_placeholder_page_is_reachable(): void
    {
        $this->get('/datenschutz')->assertOk()->assertSee('Datenschutz');
    }

    public function test_agb_placeholder_page_is_reachable(): void
    {
        $this->get('/agb')->assertOk()->assertSee('AGB');
    }

    public function test_kontakt_placeholder_page_is_reachable(): void
    {
        $this->get('/kontakt')->assertOk()->assertSee('Kontakt');
    }
}
