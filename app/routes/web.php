<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\ProfileMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(ProfileMiddleware::class)->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    include __DIR__.'/web/profile.php';
    include __DIR__.'/web/metrics.php';
    include __DIR__.'/web/reports.php';
});
