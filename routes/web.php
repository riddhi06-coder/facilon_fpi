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
Route::post('/fpi/submit', [FpiController::class, 'submit'])->name('fpi.submit');
Route::post('/fpi/new', [FpiController::class, 'newApplication'])->name('fpi.new');
Route::get('/fpi/load/{applicant}', [FpiController::class, 'load'])->name('fpi.load');
Route::get('/fpi/preview', [FpiController::class, 'preview'])->name('fpi.preview');

// UBO FPI Determination
Route::get('/ubo-fpi', [UboFpiController::class, 'index'])->name('ubo-fpi.index');
