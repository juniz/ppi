<?php

use Livewire\Volt\Volt;
use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Route;

// Volt::route('/', 'users.index');
Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->name('socialite.redirect');
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->name('socialite.callback');
