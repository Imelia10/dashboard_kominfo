<?php

// ─────────────────────────────────────────────────────────────────
// FILE: routes/web.php
// ─────────────────────────────────────────────────────────────────

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BansosController;
use App\Http\Controllers\PerikananController;

Route::get('/',            [DashboardController::class, 'home'])->name('home');

// Economy sekarang ditangani BansosController
Route::get('/economy',     [BansosController::class, 'economy'])->name('economy');

Route::get('/demographics',[DashboardController::class, 'demographics'])->name('demographics');
Route::get('/environment', [DashboardController::class, 'environment'])->name('environment');
Route::get('/education',   [DashboardController::class, 'education'])->name('education');
Route::get('/perikanan',   [PerikananController::class, 'index'])->name('perikanan');