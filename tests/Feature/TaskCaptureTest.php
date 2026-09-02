<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskCaptureTest extends TestCase
{
    use RefreshDatabase;

    public function test_today_shows_an_empty_state_with_no_tasks(): void
    {
        $this->get('/')->assertOk()->assertSee('Noch nichts erfasst');
    }

    public function test_a_task_can_be_captured_with_only_a_title(): void
    {
        $response = $this->post('/tasks', ['title' => 'Angebot Müller freigeben']);

        $response->assertRedirect(route('today'));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Angebot Müller freigeben',
            'area' => null,
            'due_at' => null,
            'status' => 'open',
        ]);
    }

    public function test_a_task_can_be_captured_with_area_and_due_date(): void
    {
        $response = $this->post('/tasks', [
            'title' => 'Flug nach München buchen',
            'area' => 'business',
            'due_at' => '2026-09-10',
        ]);

        $response->assertRedirect(route('today'));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Flug nach München buchen',
            'area' => 'business',
        ]);
        $this->assertSame('2026-09-10', Task::first()->due_at->format('Y-m-d'));
    }

    public function test_title_is_required(): void
    {
        $response = $this->post('/tasks', ['title' => '']);

        $response->assertSessionHasErrors('title');
        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_invalid_area_is_rejected(): void
    {
        $response = $this->post('/tasks', ['title' => 'Test', 'area' => 'family']);

        $response->assertSessionHasErrors('area');
    }

    public function test_captured_task_appears_on_today(): void
    {
        $this->post('/tasks', ['title' => 'Sarah wegen Budget anrufen']);

        $this->get('/')->assertOk()->assertSee('Sarah wegen Budget anrufen');
    }
}
