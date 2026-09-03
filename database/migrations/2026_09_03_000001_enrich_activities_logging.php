<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Two changes so `activities` can carry a full history, including for deleted tasks:
     * - `task_id` no longer cascades on delete — a task's log should outlive the task.
     *   Existing task_id values are preserved (copied via a temp column, not dropped),
     *   since production already has real completed/reopened history.
     * - `context` holds small, self-contained extra facts per event (e.g. was the task
     *   overdue when completed) so a later analysis doesn't have to replay other events
     *   to reconstruct state at that point in time.
     */
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->foreignId('task_id_new')->nullable()->after('task_id');
        });

        DB::table('activities')->update(['task_id_new' => DB::raw('task_id')]);

        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'task_id']);
            $table->dropForeign(['task_id']);
            $table->dropColumn('task_id');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->renameColumn('task_id_new', 'task_id');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->foreign('task_id')->references('id')->on('tasks')->nullOnDelete();
            $table->index(['user_id', 'task_id']);
            $table->json('context')->nullable()->after('new_value');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('context');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->foreignId('task_id_old')->nullable()->after('task_id')->constrained('tasks')->cascadeOnDelete();
        });

        DB::table('activities')->whereNotNull('task_id')->update(['task_id_old' => DB::raw('task_id')]);

        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'task_id']);
            $table->dropForeign(['task_id']);
            $table->dropColumn('task_id');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->renameColumn('task_id_old', 'task_id');
            $table->index(['user_id', 'task_id']);
        });
    }
};
