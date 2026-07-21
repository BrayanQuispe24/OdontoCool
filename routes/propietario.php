<?php 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdministradorController;

Route::get('/administrador', [AdministradorController::class, 'index'])
    ->name('administrador.index');

Route::post('/administrador', [AdministradorController::class, 'store'])
    ->name('administrador.store');

Route::put('/administrador/{codigo_propietario}', [AdministradorController::class, 'update'])
    ->name('administrador.update');

Route::get('/administrador/{codigo_propietario}', [AdministradorController::class, 'show'])
    ->name('administrador.show');

?>