<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/about', function () {
    return view('about');
})->name('about');

// Projects
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
Route::patch('/projects/{project}/archive', [ProjectController::class, 'archive'])->name('projects.archive');

// Tasks
Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');

// Tags
Route::get('/tags', [\App\Http\Controllers\TagController::class, 'index'])->name('tags.index');
Route::get('/tags/{tag}/edit', [\App\Http\Controllers\TagController::class, 'edit'])->name('tags.edit');
Route::put('/tags/{tag}', [\App\Http\Controllers\TagController::class, 'update'])->name('tags.update');
Route::delete('/tags/{tag}', [\App\Http\Controllers\TagController::class, 'destroy'])->name('tags.destroy');

use App\Http\Controllers\Api\TimerController;
Route::post('/api/timers/{timer}', [TimerController::class, 'update']);
Route::post('/api/timers/{timer}/stop', [TimerController::class, 'stop']);
