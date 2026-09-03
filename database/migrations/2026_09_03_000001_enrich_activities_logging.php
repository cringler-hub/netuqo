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
     * Every step below checks its own precondition via Schema::getIndexes()/
     * getForeignKeys()/hasColumn() rather than one coarse guard, because three earlier
     * versions of this migration each broke differently on production, and each failed
     * deploy left MySQL DDL changes (auto-committed per-statement, no transaction)
     * partially applied without the migration ever being recorded as run — so the next
     * deploy's `migrate --force` resumed from a state one step further drifted than the
     * one before it. This version tolerates resuming from any partial state, not just
     * the ones observed so far. See DECISIONS.md.
     */
    public function up(): void
    {
        $hasIndex = function (string $name, array $columns): bool {
            foreach (Schema::getIndexes('activities') as $index) {
                if ($index['name'] === $name && $index['columns'] === $columns) {
                    return true;
                }
            }

            return false;
        };

        // Returns the foreign key entry for the given columns, or null if none exists.
        // Dropping by column list alone (Blueprint::dropForeign(['task_id'])) assumes
        // Laravel's conventional name and fails if the real name differs — as it can
        // here, since a local rollback recreates it as `activities_task_id_old_foreign`
        // — so drops below go by the actual name where the driver reports one. SQLite
        // never names foreign keys (always reports null), so a null name isn't "not
        // found" — it falls back to the column-array form, which SQLite does support.
        $foreignKeyOn = function (array $columns): ?array {
            foreach (Schema::getForeignKeys('activities') as $foreignKey) {
                if ($foreignKey['columns'] === $columns) {
                    return $foreignKey;
                }
            }

            return null;
        };

        if (! Schema::hasColumn('activities', 'task_id_new')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->foreignId('task_id_new')->nullable()->after('task_id');
            });

            DB::table('activities')->update(['task_id_new' => DB::raw('task_id')]);
        }

        // The `user_id`+`task_id` composite index is the ONLY index InnoDB has that
        // leads with `user_id`, so it's also the supporting index for the (unrelated)
        // `activities_user_id_foreign` key — MySQL refuses to drop it without a
        // standalone `user_id` index to take over first, regardless of the task_id
        // foreign key's own state.
        if (! $hasIndex('activities_user_id_index', ['user_id'])) {
            Schema::table('activities', function (Blueprint $table) {
                $table->index('user_id', 'activities_user_id_index');
            });
        }

        if (Schema::hasColumn('activities', 'task_id')) {
            if ($foreignKey = $foreignKeyOn(['task_id'])) {
                Schema::table('activities', function (Blueprint $table) use ($foreignKey) {
                    $table->dropForeign($foreignKey['name'] ?? ['task_id']);
                });
            }

            if ($hasIndex('activities_user_id_task_id_index', ['user_id', 'task_id'])) {
                Schema::table('activities', function (Blueprint $table) {
                    $table->dropIndex(['user_id', 'task_id']);
                });
            }

            // Also drops any leftover single-column index MySQL auto-created for a
            // foreign key that a previous, partially-failed attempt already dropped.
            Schema::table('activities', function (Blueprint $table) {
                $table->dropColumn('task_id');
            });
        }

        if (Schema::hasColumn('activities', 'task_id_new')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->renameColumn('task_id_new', 'task_id');
            });
        }

        if (! $foreignKeyOn(['task_id'])) {
            Schema::table('activities', function (Blueprint $table) {
                $table->foreign('task_id')->references('id')->on('tasks')->nullOnDelete();
            });
        }

        if (! $hasIndex('activities_user_id_task_id_index', ['user_id', 'task_id'])) {
            Schema::table('activities', function (Blueprint $table) {
                $table->index(['user_id', 'task_id']);
            });
        }

        // The composite index above covers user_id again (as its leading column), so
        // the standalone helper index is redundant once both exist — drop it.
        if ($hasIndex('activities_user_id_index', ['user_id']) && $hasIndex('activities_user_id_task_id_index', ['user_id', 'task_id'])) {
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
