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
     * Written defensively (Schema::hasColumn guards) because the first two versions of
     * this migration broke on production: the `user_id`+`task_id` composite index is
     * also the only index that supports the separate `activities_user_id_foreign` key,
     * so dropping it fails on MySQL ("needed in a foreign key constraint") unless
     * `user_id` gets a standalone index first — a MySQL-only quirk SQLite never
     * surfaces, only found by reproducing production's exact state against a real
     * MySQL/MariaDB instance. That left a real, populated `task_id_new` column stuck on
     * the live table with the migration never recorded as run — every deploy's
     * `migrate --force` retried it and failed again. This version resumes correctly
     * from that exact state as well as from a clean database. See DECISIONS.md.
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
            // The real reason the first two attempts at this migration failed on
            // production (confirmed by reproducing it against a real MySQL/MariaDB
            // instance, not just SQLite, which never exercises this path): the
            // composite `user_id`+`task_id` index is the ONLY index InnoDB has that
            // leads with `user_id`, so it's also the supporting index for the
            // (unrelated) `activities_user_id_foreign` foreign key. Dropping it — no
            // matter the statement order relative to the task_id foreign key — fails
            // with "needed in a foreign key constraint" because that would leave the
            // user_id foreign key without any index at all. The fix is to give the
            // user_id foreign key its own standalone index first, so the composite
            // index is free to drop.
            Schema::table('activities', function (Blueprint $table) {
                $table->index('user_id', 'activities_user_id_index');
            });

            Schema::table('activities', function (Blueprint $table) {
                $table->dropForeign(['task_id']);
            });

            Schema::table('activities', function (Blueprint $table) {
                $table->dropIndex(['user_id', 'task_id']);
            });

            // Dropping the column also removes the single-column index MySQL
            // auto-created to support the foreign key we just dropped.
            Schema::table('activities', function (Blueprint $table) {
                $table->dropColumn('task_id');
            });

            Schema::table('activities', function (Blueprint $table) {
                $table->renameColumn('task_id_new', 'task_id');
            });

            Schema::table('activities', function (Blueprint $table) {
                $table->foreign('task_id')->references('id')->on('tasks')->nullOnDelete();
                $table->index(['user_id', 'task_id']);
            });

            // The composite index above covers user_id again (as its leading column),
            // so the standalone helper index is redundant now — drop it.
            Schema::table('activities', function (Blueprint $table) {
                $table->dropIndex('activities_user_id_index');
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

        // down() is a local-dev safety net only — it's never invoked in production
        // (deploy.yml only ever runs `migrate --force`, i.e. up()) — so the resulting
        // foreign key keeps its temp-column-derived name (activities_task_id_old_foreign)
        // rather than matching up()'s naming exactly; harmless for a rollback's own sake.
        Schema::table('activities', function (Blueprint $table) {
            $table->foreignId('task_id_old')->nullable()->after('task_id')->constrained('tasks')->cascadeOnDelete();
        });

        DB::table('activities')->whereNotNull('task_id')->update(['task_id_old' => DB::raw('task_id')]);

        // Same MySQL quirk as up(): the composite index is the user_id foreign key's
        // only supporting index, so it needs a standalone stand-in before it can drop.
        Schema::table('activities', function (Blueprint $table) {
            $table->index('user_id', 'activities_user_id_index');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'task_id']);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('task_id');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->renameColumn('task_id_old', 'task_id');
            $table->index(['user_id', 'task_id']);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex('activities_user_id_index');
        });
    }
};
