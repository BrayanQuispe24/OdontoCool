<?php

use App\Http\Controllers\SolicitudAnalisisController;
use Illuminate\Support\Facades\Route;

Route::get('/solicitud-analisis', [SolicitudAnalisisController::class, 'index'])
    ->name('solicitud_analisis.index');
Route::post('/solicitud-analisis', [SolicitudAnalisisController::class, 'store'])
    ->name('solicitud_analisis.store');
Route::get('/solicitud-analisis/{id}', [SolicitudAnalisisController::class, 'show'])
    ->name('solicitud_analisis.show');
Route::put('/solicitud-analisis/{id}', [SolicitudAnalisisController::class, 'update'])
    ->name('solicitud_analisis.update');
Route::delete('/solicitud-analisis/{id}', [SolicitudAnalisisController::class, 'destroy'])
    ->name('solicitud_analisis.destroy');
