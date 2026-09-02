<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\TodayController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TodayController::class, 'index'])->name('today');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
