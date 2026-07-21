<?php

use App\Http\Controllers\RolController;
use Illuminate\Support\Facades\Route;

Route::get('/rol', [RolController::class, 'index'])->name('rol.index');
Route::post('/rol', [RolController::class, 'store'])->name('rol.store');
Route::get('/rol/{rol_id}', [RolController::class, 'show'])->name('rol.show');
Route::put('/rol/{rol_id}', [RolController::class, 'update'])->name('rol.update');
Route::delete('/rol/{rol_id}', [RolController::class, 'destroy'])->name('rol.destroy');
Route::post('/rol/{rol_id}/asignar-permiso', [RolController::class, 'asignarPermiso'])
    ->name('rol.asignarPermiso');
Route::delete('/rol/{rol_id}/eliminar-permiso/{permiso_id}', [RolController::class, 'eliminarPermiso'])->name('rol.eliminarPermiso');
