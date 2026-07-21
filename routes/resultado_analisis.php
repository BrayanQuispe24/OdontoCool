<?php

use App\Http\Controllers\ResultadoAnalisisController;
use Illuminate\Support\Facades\Route;

Route::get('/resultado-analisis', [ResultadoAnalisisController::class, 'index'])
    ->name('resultado_analisis.index');
Route::post('/resultado-analisis', [ResultadoAnalisisController::class, 'store'])
    ->name('resultado_analisis.store');
Route::get('/resultado-analisis/{id}', [ResultadoAnalisisController::class, 'show'])
    ->name('resultado_analisis.show');
Route::put('/resultado-analisis/{id}', [ResultadoAnalisisController::class, 'update'])
    ->name('resultado_analisis.update');
Route::delete('/resultado-analisis/{id}', [ResultadoAnalisisController::class, 'destroy'])
    ->name('resultado_analisis.destroy');
