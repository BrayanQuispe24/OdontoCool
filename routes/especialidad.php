<?php

use App\Http\Controllers\EspecialidadController;
use Illuminate\Support\Facades\Route;

Route::get('/especialidad', [EspecialidadController::class, 'index'])
    ->name('especialidad.index');

Route::post('/especialidad', [EspecialidadController::class, 'store'])
    ->name('especialidad.store');

Route::get('/especialidad/{especialidad_id}', [EspecialidadController::class, 'show'])
    ->name('especialidad.show');

Route::put('/especialidad/{especialidad_id}', [EspecialidadController::class, 'update'])
    ->name('especialidad.update');

Route::delete('/especialidad/{especialidad_id}', [EspecialidadController::class, 'destroy'])
    ->name('especialidad.destroy');
