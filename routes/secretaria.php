<?php

use App\Http\Controllers\SecretariaController;
use Illuminate\Support\Facades\Route;

Route::get('/secretaria', [SecretariaController::class, 'index'])
->name('secretaria.index');

Route::post('/secretaria', [SecretariaController::class, 'store'])
->name('secretaria.store');

Route::get('/secretaria/{codigo_secretaria}', [SecretariaController::class, 'show'])
->name('secretaria.show');

Route::put('/secretaria/{codigo_secretaria}', [SecretariaController::class, 'update'])
->name('secretaria.update');

Route::post('/secretarias/{codigo_secretaria}/turnos', [SecretariaController::class, 'asignarTurno'])
    ->name('secretaria.asignar-turno');
