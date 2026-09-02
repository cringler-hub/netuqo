<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\TodayController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TodayController::class, 'index'])->name('today');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');

// TEMPORARY diagnostic routes to isolate the production 500 on POST — remove once resolved.
Route::get('/diag', fn () => view('diag'));
Route::post('/diag-ping', fn () => 'pong-with-csrf');
Route::post('/diag-ping-nocsrf', fn () => 'pong-no-csrf');
