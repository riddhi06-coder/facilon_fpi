<?php

use App\Http\Controllers\FpiController;
use App\Http\Controllers\UboFpiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// FPI Registration
Route::get('/fpi', [FpiController::class, 'index'])->name('fpi.index');
Route::post('/fpi', [FpiController::class, 'store'])->name('fpi.store');

// UBO FPI Determination
Route::get('/ubo-fpi', [UboFpiController::class, 'index'])->name('ubo-fpi.index');
