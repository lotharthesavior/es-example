<?php

use App\Http\Controllers\HealthProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/reports', [HealthProfileController::class, 'createReportForm'])->name('health.reports');
Route::get('/reports/generate', [HealthProfileController::class, 'generateReport'])->name('health.reports.generate');

Route::post('/reports/csv', [HealthProfileController::class, 'exportReportCsv'])->name('health.reports.csv');
