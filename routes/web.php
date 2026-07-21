<?php

use App\Http\Controllers\BusquedaNegocioController;
use App\Http\Controllers\PublicLandingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\UsuarioPreferenciaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReporteController;
use Inertia\Inertia;

Route::get('/', [PublicLandingController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/preferencias', [UsuarioPreferenciaController::class, 'get'])
        ->name('preferencias.get');
    Route::patch('/preferencias', [UsuarioPreferenciaController::class, 'update'])
        ->name('preferencias.update');
    Route::patch('/usuario/{codigo_usuario}/cambiar-password', [UsuarioController::class, 'cambiarPassword'])
        ->name('usuario.cambiar-password');
    Route::patch('/usuario/{codigo_usuario}/cambiar-email', [UsuarioController::class, 'cambiarEmail'])
        ->name('usuario.cambiar-email');

    Route::get('/reportes', [ReporteController::class, 'index'])
        ->name('reportes.index');
    require __DIR__.'/modulo.php';
    require __DIR__.'/permiso.php';
    require __DIR__.'/rol.php';
    require __DIR__.'/propietario.php';
    require __DIR__.'/doctor.php';
    require __DIR__.'/secretaria.php';
    require __DIR__.'/paciente.php';
    require __DIR__.'/servicio.php';
    require __DIR__.'/servicio_prestado.php';
    require __DIR__.'/receta_recomendacion.php';
    require __DIR__.'/tratamiento.php';
    require __DIR__.'/analisis.php';
    require __DIR__.'/solicitud_analisis.php';
    require __DIR__.'/resultado_analisis.php';
    require __DIR__.'/antecedentes_odontologicos.php';
    require __DIR__.'/historial_clinico.php';
    require __DIR__.'/diagnostico.php';
    require __DIR__.'/cita.php';
    require __DIR__.'/especialidad.php';
    require __DIR__.'/turno.php';
    require __DIR__.'/boleta_pago.php';
    require __DIR__.'/bitacora.php';
});

Route::get('/buscar-negocio', [BusquedaNegocioController::class, 'index'])
    ->name('buscar.negocio');

require __DIR__.'/auth.php';
// ejemplo de como usarlo en las rutas, se puede usar en cualquier ruta que necesite permisos
//  Route::get('/pacientes', function () {
//         return Inertia::render('Pacientes/Index');
//     })->middleware('permiso:pacientes.ver')
//       ->name('pacientes.index');

//     Route::get('/pacientes/create', function () {
//         return Inertia::render('Pacientes/Create');
//     })->middleware('permiso:pacientes.crear')
//       ->name('pacientes.create');
