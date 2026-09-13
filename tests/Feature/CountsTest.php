<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withSession(['gate_unlocked' => true]);
    }

    public function test_nav_shows_the_count_of_tasks_due_today(): void
    {
        $this->post('/tasks', ['title' => 'Angebot prüfen', 'due_at' => now()->format('Y-m-d')]);
        $this->post('/tasks', ['title' => 'Rechnung schreiben', 'due_at' => now()->subDay()->format('Y-m-d')]);

        $this->get('/')->assertOk()->assertSee('Heute (2)', false);
    }

    public function test_area_filter_chips_show_their_own_counts(): void
    {
        $this->post('/tasks', ['title' => 'Angebot prüfen', 'due_at' => now()->format('Y-m-d'), 'area' => 'business']);
        $this->post('/tasks', ['title' => 'Zahnarzttermin', 'due_at' => now()->format('Y-m-d'), 'area' => 'private']);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Alle (2)', false)
            ->assertSee('Business (1)', false)
            ->assertSee('Privat (1)', false);
    }

    public function test_area_filter_counts_are_unaffected_by_the_currently_applied_filter(): void
    {
        $this->post('/tasks', ['title' => 'Angebot prüfen', 'due_at' => now()->format('Y-m-d'), 'area' => 'business']);
        $this->post('/tasks', ['title' => 'Zahnarzttermin', 'due_at' => now()->format('Y-m-d'), 'area' => 'private']);

        $this->get('/?area=business')->assertOk()->assertSee('Privat (1)', false);
    }

    public function test_legal_pages_never_show_task_counts_to_an_anonymous_visitor(): void
    {
        $this->post('/tasks', ['title' => 'Angebot prüfen', 'due_at' => now()->format('Y-m-d')]);

        // A fresh, un-authenticated request — the legal pages are reachable without
        // the gate, and must never leak how many tasks exist to that visitor.
        $response = $this->withSession(['gate_unlocked' => false])->get('/impressum');

        $response->assertOk()->assertDontSee('Heute (', false);
    }
}
