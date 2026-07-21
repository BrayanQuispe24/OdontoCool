<?php

use App\Http\Controllers\PacienteController;
use Illuminate\Support\Facades\Route;

Route::get('/pacientes', [PacienteController::class, 'index'])
->name('paciente.index');

Route::post('/pacientes', [PacienteController::class, 'store'])
->name('paciente.store');

Route::put('/pacientes/{codigo_paciente}', [PacienteController::class, 'update'])
->name('paciente.update');

Route::delete('/pacientes/{codigo_paciente}', [PacienteController::class, 'destroy'])
->name('paciente.destroy');

Route::get('/pacientes/{codigo_paciente}', [PacienteController::class, 'show'])
->name('paciente.show');
