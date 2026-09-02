<?php

use App\Http\Controllers\DoneController;
use App\Http\Controllers\LaterController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TodayController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', [TodayController::class, 'index'])->name('today');
Route::get('/later', [LaterController::class, 'index'])->name('later');
Route::get('/done', [DoneController::class, 'index'])->name('done');

Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::post('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
Route::post('/tasks/{task}/reopen', [TaskController::class, 'reopen'])->name('tasks.reopen');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

// TEMPORARY diagnostic route for the "task capture feels slow" investigation.
// Remove once the bottleneck is found. See DECISIONS.md.
Route::get('/diag-perf', function () {
    $bootMs = (microtime(true) - LARAVEL_START) * 1000;

    $dbStart = microtime(true);
    DB::select('select 1');
    $dbPingMs = (microtime(true) - $dbStart) * 1000;

    return response()->json([
        'boot_ms' => round($bootMs, 1),
        'db_ping_ms' => round($dbPingMs, 1),
        'session_driver' => config('session.driver'),
        'cache_store' => config('cache.default'),
        'opcache_enabled' => function_exists('opcache_get_status') ? (opcache_get_status(false)['opcache_enabled'] ?? null) : 'unavailable',
        'php_sapi' => php_sapi_name(),
        'php_version' => PHP_VERSION,
    ]);
});
