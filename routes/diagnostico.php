<?php

use App\Http\Controllers\DiagnosticoController;
use Illuminate\Support\Facades\Route;

Route::get('/diagnostico', [DiagnosticoController::class, 'index'])
    ->name('diagnostico.index');
Route::post('/diagnostico', [DiagnosticoController::class, 'store'])
    ->name('diagnostico.store');
Route::get('/diagnostico/{id}', [DiagnosticoController::class, 'show'])
    ->name('diagnostico.show');
Route::put('/diagnostico/{id}', [DiagnosticoController::class, 'update'])
    ->name('diagnostico.update');
Route::delete('/diagnostico/{id}', [DiagnosticoController::class, 'destroy'])
    ->name('diagnostico.destroy');
