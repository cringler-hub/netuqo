<?php

use App\Http\Controllers\DoneController;
use App\Http\Controllers\LaterController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TodayController;
use App\Http\Controllers\WeekController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TodayController::class, 'index'])->name('today');
Route::get('/week', [WeekController::class, 'index'])->name('week');
Route::get('/month', [MonthController::class, 'index'])->name('month');
Route::get('/later', [LaterController::class, 'index'])->name('later');
Route::get('/done', [DoneController::class, 'index'])->name('done');

Route::view('/impressum', 'legal', ['heading' => 'Impressum'])->name('impressum');
Route::view('/datenschutz', 'legal', ['heading' => 'Datenschutz'])->name('datenschutz');
Route::view('/agb', 'legal', ['heading' => 'AGB'])->name('agb');
Route::view('/kontakt', 'legal', ['heading' => 'Kontakt'])->name('kontakt');

Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::post('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
Route::post('/tasks/{task}/reopen', [TaskController::class, 'reopen'])->name('tasks.reopen');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
