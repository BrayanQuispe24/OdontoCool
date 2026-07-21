<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecetaRecomendacionController;

Route::get('/receta-recomendacion', [RecetaRecomendacionController::class, 'index'])
    ->name('receta_recomendacion.index');
Route::post('/receta-recomendacion', [RecetaRecomendacionController::class, 'store'])
    ->name('receta_recomendacion.store');
Route::get('/receta-recomendacion/{receta_id}', [RecetaRecomendacionController::class, 'show'])
    ->name('receta_recomendacion.show');
Route::put('/receta-recomendacion/{receta_id}', [RecetaRecomendacionController::class, 'update'])
    ->name('receta_recomendacion.update');
Route::delete('/receta-recomendacion/{receta_id}', [RecetaRecomendacionController::class, 'destroy'])
    ->name('receta_recomendacion.destroy');
