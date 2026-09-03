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
     *
     * Written defensively (Schema::hasColumn guards) because the first version of this
     * migration broke on production: it dropped the user_id+task_id index before
     * dropping the task_id foreign key, which SQLite tolerates but MySQL rejects
     * ("needed in a foreign key constraint"). That left a real, populated `task_id_new`
     * column stuck on the live table with the migration never recorded as run — every
     * later deploy's `migrate --force` retried it and failed again on "column already
     * exists". This version resumes correctly from that exact state as well as from a
     * clean database. See DECISIONS.md.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('activities', 'task_id_new')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->foreignId('task_id_new')->nullable()->after('task_id');
            });

            DB::table('activities')->update(['task_id_new' => DB::raw('task_id')]);
        }

        if (Schema::hasColumn('activities', 'task_id_new')) {
            Schema::table('activities', function (Blueprint $table) {
                // Foreign key first, then the index it depends on — MySQL rejects the
                // reverse order.
                $table->dropForeign(['task_id']);
                $table->dropIndex(['user_id', 'task_id']);
                $table->dropColumn('task_id');
            });

            Schema::table('activities', function (Blueprint $table) {
                $table->renameColumn('task_id_new', 'task_id');
            });

            Schema::table('activities', function (Blueprint $table) {
                $table->foreign('task_id')->references('id')->on('tasks')->nullOnDelete();
                $table->index(['user_id', 'task_id']);
            });
        }

        if (! Schema::hasColumn('activities', 'context')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->json('context')->nullable()->after('new_value');
            });
        }
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
            $table->dropForeign(['task_id']);
            $table->dropIndex(['user_id', 'task_id']);
            $table->dropColumn('task_id');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->renameColumn('task_id_old', 'task_id');
            $table->index(['user_id', 'task_id']);
        });
    }
};
