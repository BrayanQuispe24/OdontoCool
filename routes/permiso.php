<?php

use App\Http\Controllers\PermisoController;
use Illuminate\Support\Facades\Route;

Route::get('/permiso', [PermisoController::class, 'index'])->name('permiso.index');
Route::post('/permiso', [PermisoController::class, 'store'])->name('permiso.store');
Route::get('/permiso/{permiso_id}', [PermisoController::class, 'show'])->name('permiso.show');
Route::put('/permiso/{permiso_id}', [PermisoController::class, 'update'])->name('permiso.update');
Route::delete('/permiso/{permiso_id}', [PermisoController::class, 'destroy'])->name('permiso.destroy');

