<?php

use App\Http\Controllers\DoneController;
use App\Http\Controllers\LaterController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TodayController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TodayController::class, 'index'])->name('today');
Route::get('/later', [LaterController::class, 'index'])->name('later');
Route::get('/done', [DoneController::class, 'index'])->name('done');

Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::post('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
Route::post('/tasks/{task}/reopen', [TaskController::class, 'reopen'])->name('tasks.reopen');
