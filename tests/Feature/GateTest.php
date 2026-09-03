<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['gate.password' => 'test-only-password']);
    }

    public function test_a_guest_is_redirected_to_the_gate(): void
    {
        $this->get('/')->assertRedirect(route('gate.show'));
    }

    public function test_legal_pages_stay_reachable_without_the_gate(): void
    {
        $this->get('/impressum')->assertOk();
    }

    public function test_the_wrong_password_does_not_unlock_the_gate(): void
    {
        $this->post('/login', ['password' => 'wrong'])
            ->assertSessionHasErrors('password');

        $this->get('/')->assertRedirect(route('gate.show'));
    }

    public function test_the_correct_password_unlocks_the_gate(): void
    {
        $this->post('/login', ['password' => 'test-only-password'])
            ->assertRedirect(route('today'));

        $this->get('/')->assertOk();
    }

    public function test_an_empty_configured_password_fails_closed(): void
    {
        config(['gate.password' => '']);

        $this->post('/login', ['password' => ''])
            ->assertSessionHasErrors('password');
    }

    public function test_logging_out_locks_the_gate_again(): void
    {
        $this->withSession(['gate_unlocked' => true]);

        $this->post('/logout')->assertRedirect(route('gate.show'));
        $this->get('/')->assertRedirect(route('gate.show'));
    }
}
