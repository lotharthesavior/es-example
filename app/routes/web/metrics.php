<?php

use App\Http\Controllers\HealthProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/metrics', [HealthProfileController::class, 'index'])->name('health.metrics.index');
Route::get('/metrics/create', [HealthProfileController::class, 'createMetricForm'])->name('health.metrics.create');
Route::post('/metrics', [HealthProfileController::class, 'storeMetric'])->name('health.metrics.store');
Route::get('/metrics/{metric}/edit', [HealthProfileController::class, 'editMetricForm'])->name('health.metrics.edit');
Route::delete('/metrics/{metric}', [HealthProfileController::class, 'destroy'])->name('health.metrics.destroy');
Route::put('/metrics/{metric}', [HealthProfileController::class, 'storeMetric'])->name('health.metrics.update');
