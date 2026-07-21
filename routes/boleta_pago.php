<?php

use App\Http\Controllers\BoletaPagoController;
use Illuminate\Support\Facades\Route;

Route::get('/boleta-pago', [BoletaPagoController::class, 'index'])
    ->name('boleta_pago.index');
Route::post('/boleta-pago', [BoletaPagoController::class, 'store'])
    ->name('boleta_pago.store');
Route::post('/boleta-pago/pagar', [BoletaPagoController::class, 'payCuota'])
    ->name('boleta_pago.pagar');
Route::delete('/boleta-pago/{id}', [BoletaPagoController::class, 'destroy'])
    ->name('boleta_pago.destroy');

// PagoFacil QR codes integration routes
Route::post('/boleta-pago/cuota/{id_cuota}/qr', [BoletaPagoController::class, 'generarQrCuota'])
    ->name('boleta_pago.generar_qr');
Route::post('/boleta-pago/cuota/{id_cuota}/verificar', [BoletaPagoController::class, 'verificarPagoCuota'])
    ->name('boleta_pago.verificar_qr');
Route::post('/boleta-pago/callback/{id_cuota}', [BoletaPagoController::class, 'callback'])
    ->name('boleta_pago.callback');
