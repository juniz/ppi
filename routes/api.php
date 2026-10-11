<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Mock SIMRS Simulator Routes
|--------------------------------------------------------------------------
| Digunakan untuk pengujian integrasi & bridging SiHAI's PPI
| saat server SIMRS eksternal belum tersedia.
*/
Route::prefix('mock-simrs')->group(function () {
    Route::get('/', [\App\Http\Controllers\MockSimrsApiController::class, 'info'])->name('mock.simrs.info');
    Route::get('/ruangan', [\App\Http\Controllers\MockSimrsApiController::class, 'ruangan'])->name('mock.simrs.ruangan');
    Route::get('/pasien-ranap/aktif', [\App\Http\Controllers\MockSimrsApiController::class, 'pasienRanapAktif'])->name('mock.simrs.pasien-ranap.aktif');
    Route::get('/pasien-ranap/pulang', [\App\Http\Controllers\MockSimrsApiController::class, 'pasienRanapPulang'])->name('mock.simrs.pasien-ranap.pulang');
    Route::get('/pasien/search', [\App\Http\Controllers\MockSimrsApiController::class, 'searchPasien'])->name('mock.simrs.pasien.search');
    Route::get('/pasien/{no_rawat}/alat-invasif', [\App\Http\Controllers\MockSimrsApiController::class, 'alatInvasif'])->name('mock.simrs.pasien.alat-invasif');
});
