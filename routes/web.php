<?php

use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\ChartController;
use Illuminate\Support\Facades\Route;

Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->name('socialite.redirect');
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->name('socialite.callback');
Route::get('/auth/google/complete-profile', [SocialiteController::class, 'showCompleteProfile'])
    ->name('socialite.complete-profile.show');
Route::post('/auth/google/complete-profile', [SocialiteController::class, 'storeCompleteProfile'])
    ->name('socialite.complete-profile.store');

Route::get('/', function () {
    return redirect('/admin/dashboard');
});

// Chart routes
Route::get('/chart/infeksi', [ChartController::class, 'infeksiChart'])->name('chart.infeksi');
Route::get('/chart/pemasangan', [ChartController::class, 'pemasanganChart'])->name('chart.pemasangan');
Route::post('/chart/save-image', [ChartController::class, 'saveChartImage'])->name('chart.save-image');
