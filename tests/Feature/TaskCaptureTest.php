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

    public function test_captured_task_without_due_date_appears_on_later(): void
    {
        $this->post('/tasks', ['title' => 'Sarah wegen Budget anrufen']);

        $this->get('/later')->assertOk()->assertSee('Sarah wegen Budget anrufen');
    }

    public function test_task_due_today_appears_on_today(): void
    {
        $this->post('/tasks', ['title' => 'Angebot prüfen', 'due_at' => now()->format('Y-m-d')]);

        $this->get('/')->assertOk()->assertSee('Angebot prüfen');
    }

    public function test_overdue_task_appears_on_today(): void
    {
        $this->post('/tasks', ['title' => 'Rechnung schreiben', 'due_at' => now()->subDays(2)->format('Y-m-d')]);

        $this->get('/')->assertOk()->assertSee('Rechnung schreiben');
    }

    public function test_overdue_task_is_labeled(): void
    {
        $this->post('/tasks', ['title' => 'Rechnung schreiben', 'due_at' => now()->subDays(2)->format('Y-m-d')]);

        $this->get('/')->assertOk()->assertSee('Überfällig');
    }

    public function test_task_due_today_is_not_labeled_overdue(): void
    {
        $this->post('/tasks', ['title' => 'Angebot prüfen', 'due_at' => now()->format('Y-m-d')]);

        $this->get('/')->assertOk()->assertDontSee('Überfällig');
    }

    public function test_task_due_in_the_future_appears_on_later(): void
    {
        $this->post('/tasks', ['title' => 'Reise planen', 'due_at' => now()->addDays(5)->format('Y-m-d')]);

        $this->get('/')->assertOk()->assertDontSee('Reise planen');
        $this->get('/later')->assertOk()->assertSee('Reise planen');
    }

    public function test_a_task_can_be_marked_as_done(): void
    {
        $this->post('/tasks', ['title' => 'Steuererklärung abschicken', 'due_at' => now()->format('Y-m-d')]);
        $task = Task::first();

        $response = $this->post(route('tasks.complete', $task));

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'done']);
        $this->get('/')->assertOk()->assertDontSee('Steuererklärung abschicken');
        $this->get('/done')->assertOk()->assertSee('Steuererklärung abschicken');
    }

    public function test_a_done_task_can_be_reopened(): void
    {
        $this->post('/tasks', ['title' => 'Termin verschieben', 'due_at' => now()->format('Y-m-d')]);
        $task = Task::first();
        $this->post(route('tasks.complete', $task));

        $response = $this->post(route('tasks.reopen', $task));

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'open']);
        $this->get('/done')->assertOk()->assertDontSee('Termin verschieben');
        $this->get('/')->assertOk()->assertSee('Termin verschieben');
    }

    public function test_a_tasks_due_date_can_be_changed_afterwards(): void
    {
        $this->post('/tasks', ['title' => 'Workshop vorbereiten', 'due_at' => now()->addDays(5)->format('Y-m-d')]);
        $task = Task::first();

        $response = $this->patch(route('tasks.update', $task), ['due_at' => now()->format('Y-m-d')]);

        $response->assertRedirect();
        $this->assertSame(now()->format('Y-m-d'), $task->fresh()->due_at->format('Y-m-d'));
        $this->get('/')->assertOk()->assertSee('Workshop vorbereiten');
        $this->get('/later')->assertOk()->assertDontSee('Workshop vorbereiten');
    }

    public function test_a_tasks_due_date_can_be_cleared(): void
    {
        $this->post('/tasks', ['title' => 'Ohne Termin', 'due_at' => now()->format('Y-m-d')]);
        $task = Task::first();

        $this->patch(route('tasks.update', $task), ['due_at' => '']);

        $this->assertNull($task->fresh()->due_at);
    }

    public function test_tasks_can_be_filtered_by_area(): void
    {
        $this->post('/tasks', ['title' => 'Angebot senden', 'area' => 'business', 'due_at' => now()->format('Y-m-d')]);
        $this->post('/tasks', ['title' => 'Geschenk kaufen', 'area' => 'private', 'due_at' => now()->format('Y-m-d')]);

        $this->get('/?area=business')->assertOk()->assertSee('Angebot senden')->assertDontSee('Geschenk kaufen');
        $this->get('/?area=private')->assertOk()->assertSee('Geschenk kaufen')->assertDontSee('Angebot senden');
        $this->get('/')->assertOk()->assertSee('Angebot senden')->assertSee('Geschenk kaufen');
    }

    public function test_a_task_can_be_deleted(): void
    {
        $this->post('/tasks', ['title' => 'Alten Entwurf verwerfen', 'due_at' => now()->format('Y-m-d')]);
        $task = Task::first();

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
        $this->get('/')->assertOk()->assertDontSee('Alten Entwurf verwerfen');
    }

    public function test_a_tasks_title_can_be_changed_afterwards(): void
    {
        $this->post('/tasks', ['title' => 'Alter Titel', 'due_at' => now()->format('Y-m-d')]);
        $task = Task::first();

        $response = $this->patch(route('tasks.update', $task), ['title' => 'Neuer Titel']);

        $response->assertRedirect();
        $this->assertSame('Neuer Titel', $task->fresh()->title);
        $this->get('/')->assertOk()->assertSee('Neuer Titel')->assertDontSee('Alter Titel');
    }

    public function test_a_tasks_title_cannot_be_cleared(): void
    {
        $this->post('/tasks', ['title' => 'Wichtiger Titel', 'due_at' => now()->format('Y-m-d')]);
        $task = Task::first();

        $response = $this->patch(route('tasks.update', $task), ['title' => '']);

        $response->assertSessionHasErrors('title');
        $this->assertSame('Wichtiger Titel', $task->fresh()->title);
    }

    public function test_a_tasks_area_can_be_set_afterwards(): void
    {
        $this->post('/tasks', ['title' => 'Ohne Kategorie']);
        $task = Task::first();

        $response = $this->patch(route('tasks.update', $task), ['area' => 'business']);

        $response->assertRedirect();
        $this->assertSame('business', $task->fresh()->area);
    }

    public function test_a_tasks_area_can_be_changed_afterwards(): void
    {
        $this->post('/tasks', ['title' => 'Falsch einsortiert', 'area' => 'private']);
        $task = Task::first();

        $this->patch(route('tasks.update', $task), ['area' => 'business']);

        $this->assertSame('business', $task->fresh()->area);
    }

    public function test_a_tasks_area_can_be_removed(): void
    {
        $this->post('/tasks', ['title' => 'Doch keine Kategorie', 'area' => 'business']);
        $task = Task::first();

        $this->patch(route('tasks.update', $task), ['area' => '']);

        $this->assertNull($task->fresh()->area);
    }

    public function test_later_groups_tasks_into_this_week_this_month_and_later(): void
    {
        $this->post('/tasks', ['title' => 'Diese Woche fällig', 'due_at' => now()->endOfWeek()->format('Y-m-d')]);
        $this->post('/tasks', ['title' => 'Diesen Monat fällig', 'due_at' => now()->endOfMonth()->format('Y-m-d')]);
        $this->post('/tasks', ['title' => 'Ohne Termin']);

        $response = $this->get('/later')->assertOk();
        $response->assertSeeInOrder(['Diese Woche', 'Diese Woche fällig', 'Diesen Monat', 'Diesen Monat fällig', 'Später', 'Ohne Termin']);
    }

    public function test_later_omits_empty_groups(): void
    {
        $this->post('/tasks', ['title' => 'Ohne Termin']);

        $response = $this->get('/later')->assertOk();
        $content = $response->getContent();

        // "Diese Woche"/"Diesen Monat" still appear once each as filter tabs, but not as
        // section headings, since both groups are empty.
        $this->assertSame(1, substr_count($content, 'Diese Woche'));
        $this->assertSame(1, substr_count($content, 'Diesen Monat'));
        $response->assertSee('Später');
    }

    public function test_later_range_filter_narrows_to_one_group(): void
    {
        $this->post('/tasks', ['title' => 'Diese Woche fällig', 'due_at' => now()->endOfWeek()->format('Y-m-d')]);
        $this->post('/tasks', ['title' => 'Ohne Termin']);

        $this->get('/later?range=week')
            ->assertOk()
            ->assertSee('Diese Woche fällig')
            ->assertDontSee('Ohne Termin');

        $this->get('/later?range=later')
            ->assertOk()
            ->assertSee('Ohne Termin')
            ->assertDontSee('Diese Woche fällig');
    }

    public function test_done_task_shows_when_it_was_completed(): void
    {
        $this->post('/tasks', ['title' => 'Steuererklärung abschicken']);
        $task = Task::first();

        $this->post(route('tasks.complete', $task));

        $this->get('/done')->assertOk()->assertSee('Erledigt · '.now()->format('d.m.Y'));
    }
}
