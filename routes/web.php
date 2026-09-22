<?php

use App\Http\Controllers\FpiController;
use Illuminate\Support\Facades\Route;

// FPI Registration
Route::get('/', [FpiController::class, 'start'])->name('fpi.start');
Route::post('/fpi/start', [FpiController::class, 'begin'])->name('fpi.begin');
// The form is served at a type-specific URL so the route reflects the applicant kind.
Route::get('/fpi/individual', [FpiController::class, 'index'])->name('fpi.individual');
Route::get('/fpi/non-individual', [FpiController::class, 'index'])->name('fpi.non-individual');
Route::post('/fpi', [FpiController::class, 'store'])->name('fpi.store');
Route::post('/fpi/submit', [FpiController::class, 'submit'])->name('fpi.submit');
Route::post('/fpi/new', [FpiController::class, 'newApplication'])->name('fpi.new');
Route::get('/fpi/load/{applicant}', [FpiController::class, 'load'])->name('fpi.load');
Route::get('/fpi/preview', [FpiController::class, 'preview'])->name('fpi.preview');
