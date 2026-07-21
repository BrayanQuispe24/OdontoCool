<?php

use App\Http\Controllers\ServicioController;
use Illuminate\Support\Facades\Route;

Route::get('/servicio', [ServicioController::class, 'index'])->name('servicio.index');
Route::post('/servicio', [ServicioController::class, 'store'])->name('servicio.store');
Route::get('/servicio/{servicio_id}', [ServicioController::class, 'show'])->name('servicio.show');
Route::put('/servicio/{servicio_id}', [ServicioController::class, 'update'])->name('servicio.update');
Route::delete('/servicio/{servicio_id}', [ServicioController::class, 'destroy'])->name('servicio.destroy');
Route::patch('/servicio/{servicio_id}/restore', [ServicioController::class, 'restore'])->name('servicio.restore');

Route::post('/servicio/{servicio_id}/asignar-precio', [ServicioController::class, 'asignarPrecio'])
    ->name('servicio.asignarPrecio');
Route::delete('/servicio/{servicio_id}/eliminar-precio/{asignacion_id}', [ServicioController::class, 'eliminarPrecio'])
    ->name('servicio.eliminarPrecio');
