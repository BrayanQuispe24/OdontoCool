<?php

use App\Http\Controllers\AnalisisController;
use Illuminate\Support\Facades\Route;

Route::get('/analisis', [AnalisisController::class, 'index'])
    ->name('analisis.index');
Route::post('/analisis', [AnalisisController::class, 'store'])
    ->name('analisis.store');
Route::get('/analisis/{id}', [AnalisisController::class, 'show'])
    ->name('analisis.show');
Route::put('/analisis/{id}', [AnalisisController::class, 'update'])
    ->name('analisis.update');
Route::delete('/analisis/{id}', [AnalisisController::class, 'destroy'])
    ->name('analisis.destroy');
Route::patch('/analisis/{id}/restore', [AnalisisController::class, 'restore'])
    ->name('analisis.restore');
