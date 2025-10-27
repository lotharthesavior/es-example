<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/profile/set', [ProfileController::class, 'setProfile'])->name('set.profile');
Route::get('/profiles', [ProfileController::class, 'index'])->name('profiles.index');

Route::get('/profiles/create', [ProfileController::class, 'create'])->name('profiles.create');
Route::get('/profiles/{profile}/edit', [ProfileController::class, 'edit'])->name('profiles.edit');
Route::delete('/profiles/{profile}', [ProfileController::class, 'destroy'])->name('profiles.destroy');
Route::put('/profiles/{profile}', [ProfileController::class, 'store'])->name('profiles.store');
Route::post('/profiles', [ProfileController::class, 'store'])->name('profiles.create-post');
