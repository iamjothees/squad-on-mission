<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('components.⚡dashboard');
})->name('dashboard');

Route::get('/about', function () {
    return view('components.⚡about');
})->name('about');
