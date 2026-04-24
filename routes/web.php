<?php

// ─────────────────────────────────────────────────────────────────
// FILE: routes/web.php
// ─────────────────────────────────────────────────────────────────

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BansosController;
use App\Http\Controllers\DesaController;
use App\Http\Controllers\PerikananController;
<<<<<<< HEAD
use App\Http\Controllers\EnvironmentController;


=======
use App\Http\Controllers\MapController;
>>>>>>> backup-faiq
Route::get('/',            [DashboardController::class, 'home'])->name('home');

// Economy sekarang ditangani BansosController
Route::get('/economy',     [BansosController::class, 'economy'])->name('economy');

<<<<<<< HEAD
Route::get('/demographics',[DashboardController::class, 'demographics'])->name('demographics');
Route::get('/environment', [EnvironmentController::class, 'index'])->name('environment');
=======
Route::get('/demographics',[DesaController::class, 'demographics'])->name('demographics');
Route::get('/environment', [DashboardController::class, 'environment'])->name('environment');
>>>>>>> backup-faiq
Route::get('/education',   [DashboardController::class, 'education'])->name('education');
Route::get('/perikanan',   [PerikananController::class, 'index'])->name('perikanan');
Route::get('/api/geojson', [MapController::class, 'geojson']);
Route::get('/api/statistik', [MapController::class, 'statistik']);