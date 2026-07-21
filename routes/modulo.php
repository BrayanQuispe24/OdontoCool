<?php

use App\Http\Controllers\ModuloController;
use Illuminate\Support\Facades\Route;

Route::get('/modulo', [ModuloController::class, 'index'])
    ->name('modulo.index');

Route::post('/modulo', [ModuloController::class, 'store'])
    ->name('modulo.store');

Route::put('/modulo/{modulo_id}', [ModuloController::class, 'update'])
    ->name('modulo.update');
    
Route::delete('/modulo/{modulo_id}', [ModuloController::class, 'destroy'])
    ->name('modulo.destroy');