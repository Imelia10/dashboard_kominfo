<?php

// ─────────────────────────────────────────────────────────────────
// FILE: routes/web.php  — FINAL (tanpa duplikat)
// ─────────────────────────────────────────────────────────────────

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BansosController;
use App\Http\Controllers\DesaController;
use App\Http\Controllers\PerikananController;
use App\Http\Controllers\EnvironmentController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\KemiskinanController;
use App\Http\Controllers\AgriController;
use App\Http\Controllers\BencanaController;
use App\Http\Controllers\KepadatanController;

// ── Halaman Utama ──────────────────────────────────────────────
Route::get('/', [DashboardController::class, 'home'])->name('home');

// ── Sosial & Ekonomi ───────────────────────────────────────────
Route::get('/economy',    [BansosController::class,   'economy'])->name('economy');
Route::get('/kemiskinan', [KemiskinanController::class,'index'])->name('kemiskinan');

// ── Demografi — pakai DesaController (hapus duplikat) ─────────
Route::get('/demographics', [DesaController::class, 'demographics'])->name('demographics');

// ── Sektoral ───────────────────────────────────────────────────
Route::get('/environment', [EnvironmentController::class, 'index'])->name('environment');
Route::get('/perikanan',   [PerikananController::class,   'index'])->name('perikanan');
Route::get('/agri',        [AgriController::class,        'index'])->name('agri');
Route::get('/bencana',   [BencanaController::class, 'index'])->name('bencana');  // ← DIG

// ── API ────────────────────────────────────────────────────────
Route::prefix('api')->group(function () {
    Route::get('/geojson',   [MapController::class, 'geojson']);
    Route::get('/statistik', [MapController::class, 'statistik']);
    Route::get('/bencana/map',   [MapController::class, 'bencanaGeojson']);
});


 
// ── Route halaman kepadatan ─────────────────────────────────────
Route::get('/kepadatan', [KepadatanController::class, 'index'])->name('kepadatan');
 
// ── API kepadatan ───────────────────────────────────────────────
Route::prefix('api')->group(function () {
    // (sudah ada) Route::get('/geojson',   [MapController::class, 'geojson']);
    // (sudah ada) Route::get('/statistik', [MapController::class, 'statistik']);
 
    // TAMBAH BARIS BARU:
    Route::get('/kepadatan',        [KepadatanController::class, 'apiData']);
    Route::get('/kepadatan/trend',  [KepadatanController::class, 'apiTrend']);
    Route::get('/kepadatan/umur',   [KepadatanController::class, 'apiUmur']);
});