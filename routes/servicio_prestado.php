<?php

use App\Http\Controllers\ServicioPrestadoController;
use Illuminate\Support\Facades\Route;

Route::get('/servicio-prestado', [ServicioPrestadoController::class, 'index'])
    ->name('servicio_prestado.index');
Route::post('/servicio-prestado', [ServicioPrestadoController::class, 'store'])
    ->name('servicio_prestado.store');
Route::get('/servicio-prestado/{id}', [ServicioPrestadoController::class, 'show'])
    ->name('servicio_prestado.show');
Route::put('/servicio-prestado/{id}', [ServicioPrestadoController::class, 'update'])
    ->name('servicio_prestado.update');
Route::delete('/servicio-prestado/{id}', [ServicioPrestadoController::class, 'destroy'])
    ->name('servicio_prestado.destroy');
