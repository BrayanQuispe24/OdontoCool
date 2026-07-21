<?php

use App\Http\Controllers\DoctorController;
use Illuminate\Support\Facades\Route;

Route::get('/doctor', [DoctorController::class, 'index'])
    ->name('doctor.index');

Route::post('/doctor', [DoctorController::class, 'store'])
    ->name('doctor.store');

Route::put('/doctor/{codigo_doctor}', [DoctorController::class, 'update'])
    ->name('doctor.update');

Route::get('/doctor/{codigo_doctor}', [DoctorController::class, 'show'])
    ->name('doctor.show');

Route::post('/doctores/{codigo_doctor}/especialidades', [DoctorController::class, 'asignarEspecialidad'])
    ->name('doctor.asignar-especialidad');

Route::post('/doctores/{codigo_doctor}/turnos', [DoctorController::class, 'asignarTurno'])
    ->name('doctor.asignar-turno');
