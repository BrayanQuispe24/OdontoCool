<?php

use App\Http\Controllers\HistorialClinicoController;
use Illuminate\Support\Facades\Route;

Route::get('/historial-clinico', [HistorialClinicoController::class, 'index'])
    ->name('historial_clinico.index');
Route::post('/historial-clinico', [HistorialClinicoController::class, 'store'])
    ->name('historial_clinico.store');
Route::get('/historial-clinico/{codigo_historial}', [HistorialClinicoController::class, 'show'])
    ->name('historial_clinico.show');
Route::put('/historial-clinico/{codigo_historial}', [HistorialClinicoController::class, 'update'])
    ->name('historial_clinico.update');
Route::delete('/historial-clinico/{codigo_historial}', [HistorialClinicoController::class, 'destroy'])
    ->name('historial_clinico.destroy');
