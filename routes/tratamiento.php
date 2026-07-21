<?php

use App\Http\Controllers\TratamientoController;
use Illuminate\Support\Facades\Route;

Route::get('/tratamiento', [TratamientoController::class, 'index'])
    ->name('tratamiento.index');
Route::post('/tratamiento', [TratamientoController::class, 'store'])
    ->name('tratamiento.store');
Route::get('/tratamiento/{tratamiento_id}', [TratamientoController::class, 'show'])
    ->name('tratamiento.show');
Route::put('/tratamiento/{tratamiento_id}', [TratamientoController::class, 'update'])
    ->name('tratamiento.update');
Route::delete('/tratamiento/{tratamiento_id}', [TratamientoController::class, 'destroy'])
    ->name('tratamiento.destroy');
Route::patch('/tratamiento/{tratamiento_id}/restore', [TratamientoController::class, 'restore'])
    ->name('tratamiento.restore');
