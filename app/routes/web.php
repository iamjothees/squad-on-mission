<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Api\TimerController;


use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'showLinkRequestForm'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'sendResetLinkEmail'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [\App\Http\Controllers\PasswordResetController::class, 'showResetForm'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'reset'])->middleware('guest')->name('password.update');

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [\App\Http\Controllers\VerificationController::class, 'show'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [\App\Http\Controllers\VerificationController::class, 'verify'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', [\App\Http\Controllers\VerificationController::class, 'send'])->middleware('throttle:6,1')->name('verification.send');
});

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');



// Clients
    // Leads
    Route::get('/leads', function () { return view('leads.index'); })->name('leads.index');

    // Ideas
    Route::get('/ideas', function () { return view('ideas.index'); })->name('ideas.index');

    Route::resource('clients', ClientController::class);
    Route::resource('users', \App\Http\Controllers\UserController::class);

// Projects
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::patch('/projects/{project}/archive', [ProjectController::class, 'archive'])->name('projects.archive');

// Tasks
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::patch('/tasks/{task}/next-status', [TaskController::class, 'nextStatus'])->name('tasks.next-status');

// Tags
    Route::get('/tags', [\App\Http\Controllers\TagController::class, 'index'])->name('tags.index');
    Route::get('/tags/{tag}/edit', [\App\Http\Controllers\TagController::class, 'edit'])->name('tags.edit');
    Route::put('/tags/{tag}', [\App\Http\Controllers\TagController::class, 'update'])->name('tags.update');
    Route::delete('/tags/{tag}', [\App\Http\Controllers\TagController::class, 'destroy'])->name('tags.destroy');


    Route::post('/api/timers/{timer}', [TimerController::class, 'update']);
    Route::post('/api/timers/{timer}/stop', [TimerController::class, 'stop']);
    Route::get('/timers/unassigned', [\App\Http\Controllers\TimerViewController::class, 'unassigned'])->name('timers.unassigned');
    Route::post('/timers/bulk-assign', [\App\Http\Controllers\TimerViewController::class, 'bulkAssign'])->name('timers.bulk-assign');
    Route::patch('/timers/{timer}/assign', [\App\Http\Controllers\TimerViewController::class, 'assign'])->name('timers.assign');
    Route::post('/timers', [\App\Http\Controllers\TimerViewController::class, 'store'])->name('timers.store');
    Route::get('/timers/{timer}', [\App\Http\Controllers\TimerViewController::class, 'show'])->name('timers.show');
    Route::delete('/timers/{timer}', [\App\Http\Controllers\TimerViewController::class, 'destroy'])->name('timers.destroy');
    
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/timers/{timer}/logs/{log}', [\App\Http\Controllers\TimerViewController::class, 'updateLog'])->name('timers.logs.update');
    Route::post('/timers/{timer}/logs', [\App\Http\Controllers\TimerViewController::class, 'storeLog'])->name('timers.logs.store');



});
