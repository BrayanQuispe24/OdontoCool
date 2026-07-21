<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cita\StoreCitaRequest;
use App\Http\Requests\Cita\UpdateCitaRequest;
use App\Models\Cita;
use App\Models\Doctor;
use App\Models\EstadoCita;
use App\Models\Paciente;
use App\Models\Secretaria;
use Carbon\Carbon;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CitaController extends Controller
{
    public function index()
    {
        try {
            /** @var \App\Models\User|null $currentUser */
            $currentUser = Auth::user();

            if (! $currentUser) {
                return redirect()
                    ->route('login')
                    ->with('error', 'Debes iniciar sesión.')
                    ->with('flash_id', uniqid());
            }

            $currentUser->load('rol');

            $pacienteLogueado = null;

            $queryCitas = Cita::select(
                'id_cita',
                'fecha_cita',
                'hora_inicio',
                'hora_fin',
                'motivo',
                'observacion',
                'paciente_ci',
                'codigo_historial',
                'secretaria_ci',
                'doctor_ci',
            )
                ->with([
                    'paciente:codigo_paciente,persona_id,contacto_emergencia,telefono_emergencia',
                    'paciente.persona:carnet_identidad,nombre,apellido,telefono,fecha_nacimiento,direccion,genero',

                    'secretaria:codigo_secretaria,persona_id,fecha_contratacion',
                    'secretaria.persona:carnet_identidad,nombre,apellido,telefono,fecha_nacimiento,direccion,genero',

                    'doctor:codigo_doctor,persona_id,matricula_profesional,experiencia,telefono_profesional,fecha_contratacion',
                    'doctor.persona:carnet_identidad,nombre,apellido,telefono,fecha_nacimiento,direccion,genero',

                    'ultimoEstadoAsignado.estadoCita:id,nombre',
                ]);

            if ($currentUser->rol?->nombre === 'Paciente') {
                $pacienteLogueado = Paciente::where('persona_id', $currentUser->persona_id)->first();

                if (! $pacienteLogueado) {
                    return redirect()
                        ->back()
                        ->with('error', 'No se encontró un paciente asociado a este usuario.')
                        ->with('flash_id', uniqid());
                }

                $queryCitas->where('paciente_ci', $pacienteLogueado->codigo_paciente);
            } elseif ($currentUser->rol?->nombre === 'Doctor') {
                $doctorLogueado = Doctor::where('persona_id', $currentUser->persona_id)->first();

                if (! $doctorLogueado) {
                    return redirect()
                        ->back()
                        ->with('error', 'No se encontró un doctor asociado a este usuario.')
                        ->with('flash_id', uniqid());
                }

                $queryCitas->where('doctor_ci', $doctorLogueado->codigo_doctor);
            }

            $citas = $queryCitas->get();

            $estadoCitas = EstadoCita::all();

            $queryPacientes = Paciente::select(
                'codigo_paciente',
                'persona_id',
                'contacto_emergencia',
                'telefono_emergencia'
            )
                ->with([
                    'persona:carnet_identidad,nombre,apellido,telefono,fecha_nacimiento,direccion,genero',
                    'persona.usuarios:codigo_usuario,email,url,estado,persona_id,rol_id',
                    'persona.usuarios.rol:id,nombre',
                ]);

            if ($currentUser->rol?->nombre === 'Paciente' && $pacienteLogueado) {
                $queryPacientes->where('codigo_paciente', $pacienteLogueado->codigo_paciente);
            }

            $pacientes = $queryPacientes->get();

            $doctores = Doctor::select(
                'codigo_doctor',
                'persona_id',
                'matricula_profesional',
                'experiencia',
                'telefono_profesional',
                'fecha_contratacion'
            )
                ->with([
                    'persona:carnet_identidad,nombre,apellido,telefono,fecha_nacimiento,direccion,genero',
                    'persona.usuarios:codigo_usuario,email,url,estado,persona_id,rol_id',
                    'persona.usuarios.rol:id,nombre',
                    'turnos'
                ])
                ->get();

            $secretarias = Secretaria::select(
                'codigo_secretaria',
                'persona_id',
                'fecha_contratacion'
            )
                ->with([
                    'persona:carnet_identidad,nombre,apellido,telefono,fecha_nacimiento,direccion,genero',
                    'persona.usuarios:codigo_usuario,email,url,estado,persona_id,rol_id',
                    'persona.usuarios.rol:id,nombre',
                ])
                ->get();   

            return Inertia::render('Cita/Index', [
                'citas' => $citas,
                'pacientes' => $pacientes,
                'doctores' => $doctores,
                'secretarias' => $secretarias,
                'estadoCitas' => $estadoCitas,
            ]);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al obtener las citas: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function store(StoreCitaRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $datosCita = $request->only([
                'fecha_cita',
                'hora_inicio',
                // 'hora_fin',
                'motivo',
                'observacion',
                'paciente_ci',
                // 'codigo_historial',
                // 'secretaria_ci',
                'doctor_ci',
            ]);

            if (isset($data['codigo_historial'])) {
                $datosCita['codigo_historial'] = $data['codigo_historial'];
            }
            if (isset($data['secretaria_ci'])) {
                $datosCita['secretaria_ci'] = $data['secretaria_ci'];
            }
            $horaInicio = Carbon::createFromFormat('H:i', $data['hora_inicio']);
            $horaFin = $horaInicio->copy()->addHour();

            $data['hora_inicio'] = $horaInicio->format('H:i:s');
            $data['hora_fin'] = $horaFin->format('H:i:s');

            $existeChoque = Cita::where('fecha_cita', $data['fecha_cita'])
                ->where('doctor_ci', $data['doctor_ci'])
                ->where(function ($query) use ($data) {
                    $query->where('hora_inicio', '<', $data['hora_fin'])
                        ->where('hora_fin', '>', $data['hora_inicio']);
                })
                ->exists();

            if ($existeChoque) {
                DB::rollBack();

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'El doctor ya tiene una cita registrada en ese horario.')
                    ->with('flash_id', uniqid());
            }
            $estadoCitaId = $data['estado_cita_id'] ?? 1;

            unset($data['estado_cita_id']);

            $cita = Cita::create($data);

            $cita->asignacionesEstadoCita()->create([
                'estado_cita_id' => $estadoCitaId,
                'observacion' => 'Estado inicial de la cita',
            ]);
            DB::commit();

            return redirect()
                ->route('citas.index')
                ->with('success', 'Cita creada exitosamente.')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Error al registrar la cita: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function show(int $id_cita)
    {
        try {
            $cita = Cita::with([
                'paciente:codigo_paciente,persona_id,contacto_emergencia,telefono_emergencia',
                'paciente.persona:carnet_identidad,nombre,apellido,telefono,fecha_nacimiento,direccion,genero',

                'secretaria:codigo_secretaria,persona_id,fecha_contratacion',
                'secretaria.persona:carnet_identidad,nombre,apellido,telefono,fecha_nacimiento,direccion,genero',

                'doctor:codigo_doctor,persona_id,matricula_profesional,experiencia,telefono_profesional,fecha_contratacion',
                'doctor.persona:carnet_identidad,nombre,apellido,telefono,fecha_nacimiento,direccion,genero',

                'ultimoEstadoAsignado.estadoCita:id,nombre',
                'asignacionesEstadoCita.estadoCita:id,nombre',
            ])->findOrFail($id_cita);

            return Inertia::render('Cita/Show', [
                'cita' => $cita,
            ]);

        } catch (\Exception $e) {
            return redirect()
                ->route('citas.index')
                ->with('error', 'Error al obtener la cita: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function update(UpdateCitaRequest $request, int $cita_id)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $cita = Cita::findOrFail($cita_id);

            $fechaCita = $data['fecha_cita'] ?? $cita->fecha_cita;
            $doctorCi = $data['doctor_ci'] ?? $cita->doctor_ci;

            if (isset($data['hora_inicio'])) {
                $horaInicio = Carbon::createFromFormat('H:i', $data['hora_inicio']);
                $horaFin = $horaInicio->copy()->addHour();

                $data['hora_inicio'] = $horaInicio->format('H:i:s');
                $data['hora_fin'] = $horaFin->format('H:i:s');
            }

            $horaInicioValidar = $data['hora_inicio'] ?? $cita->hora_inicio;
            $horaFinValidar = $data['hora_fin'] ?? $cita->hora_fin;

            $existeChoque = Cita::where('fecha_cita', $fechaCita)
                ->where('doctor_ci', $doctorCi)
                ->where('id_cita', '!=', $cita_id)
                ->where(function ($query) use ($horaInicioValidar, $horaFinValidar) {
                    $query->where('hora_inicio', '<', $horaFinValidar)
                        ->where('hora_fin', '>', $horaInicioValidar);
                })
                ->exists();

            if ($existeChoque) {
                DB::rollBack();

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'El doctor ya tiene otra cita registrada en ese horario.')
                    ->with('flash_id', uniqid());
            }

            if (isset($data['estado_cita_id'])) {
                $estadoCitaId = $data['estado_cita_id'];

                unset($data['estado_cita_id']);

                $cita->asignacionesEstadoCita()->create([
                    'estado_cita_id' => $estadoCitaId,
                    'observacion' => 'Cambio de estado de la cita',
                ]);
            }

            $cita->update($data);

            DB::commit();

            return redirect()
                ->route('citas.index')
                ->with('success', 'Cita actualizada exitosamente.')
                ->with('flash_id', uniqid());

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar la cita: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function cambiarEstado(HttpRequest $request, int $cita_id)
    {
        $request->validate([
            'estado_cita_id' => ['required', Rule::exists('estado_cita', 'id')],
            'observacion' => ['nullable','string','max:255'],
        ]);

        try {
            DB::beginTransaction();

            $cita = Cita::findOrFail($cita_id);

            $cita->asignacionesEstadoCita()->create([
                'estado_cita_id' => $request->estado_cita_id,
                'observacion' => $request->observacion,
            ]);

            DB::commit();

            return redirect()
                ->route('citas.index')
                ->with('success', 'Estado de la cita actualizado exitosamente.')
                ->with('flash_id', uniqid());

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al cambiar el estado de la cita: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}
