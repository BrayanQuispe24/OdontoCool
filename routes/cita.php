<?php

use App\Http\Controllers\CitaController;
use Illuminate\Support\Facades\Route;

Route::get('/citas', [CitaController::class, 'index'])
->name('citas.index');

Route::post('/citas', [CitaController::class, 'store'])
->name('citas.store');

Route::get('/citas/{id_cita}', [CitaController::class, 'show'])
->name('citas.show');

Route::put('/citas/{id_cita}', [CitaController::class, 'update'])
->name('citas.update');

Route::delete('/citas/{id_cita}', [CitaController::class, 'destroy'])
->name('citas.destroy');

Route::put('/citas/{id_cita}/cambiar-estado', [CitaController::class, 'cambiarEstado'])
->name('citas.cambiar-estado');
