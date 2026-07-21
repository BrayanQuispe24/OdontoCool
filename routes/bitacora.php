<?php

use App\Http\Controllers\BitacoraController;
use Illuminate\Support\Facades\Route;

Route::get('/bitacora', [BitacoraController::class, 'index'])
    ->name('bitacora.index');
Route::post('/bitacora', [BitacoraController::class, 'store'])
    ->name('bitacora.store');
Route::get('/bitacora/{id}', [BitacoraController::class, 'show'])
    ->name('bitacora.show');
