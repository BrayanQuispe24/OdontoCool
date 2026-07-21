<?php

use App\Http\Controllers\TurnoController;
use Illuminate\Support\Facades\Route;

Route::get('/turnos', [TurnoController::class, 'index'])
    ->name('turnos.index');

Route::post('/turnos', [TurnoController::class, 'store'])
    ->name('turnos.store');

Route::get('/turnos/{turno_id}', [TurnoController::class, 'show'])
    ->name('turnos.show');

Route::put('/turnos/{turno_id}', [TurnoController::class, 'update'])
    ->name('turnos.update');
    
Route::delete('/turnos/{turno_id}', [TurnoController::class, 'destroy'])
    ->name('turnos.destroy');