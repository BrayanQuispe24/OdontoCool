<?php

use App\Http\Controllers\AntecedentesOdontologicosController;
use Illuminate\Support\Facades\Route;

Route::get('/antecedentes-odontologicos', [AntecedentesOdontologicosController::class, 'index'])
    ->name('antecedentes_odontologicos.index');
Route::post('/antecedentes-odontologicos', [AntecedentesOdontologicosController::class, 'store'])
    ->name('antecedentes_odontologicos.store');
Route::get('/antecedentes-odontologicos/{id}', [AntecedentesOdontologicosController::class, 'show'])
    ->name('antecedentes_odontologicos.show');
Route::put('/antecedentes-odontologicos/{id}', [AntecedentesOdontologicosController::class, 'update'])
    ->name('antecedentes_odontologicos.update');
Route::delete('/antecedentes-odontologicos/{id}', [AntecedentesOdontologicosController::class, 'destroy'])
    ->name('antecedentes_odontologicos.destroy');

