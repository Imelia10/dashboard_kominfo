<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/',           [DashboardController::class, 'home'])->name('home');
Route::get('/economy',    [DashboardController::class, 'economy'])->name('economy');
Route::get('/demographics',[DashboardController::class, 'demographics'])->name('demographics');
Route::get('/health',     [DashboardController::class, 'health'])->name('health');
Route::get('/environment',[DashboardController::class, 'environment'])->name('environment');
Route::get('/education',  [DashboardController::class, 'education'])->name('education');
