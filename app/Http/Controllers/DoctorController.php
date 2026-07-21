<?php

namespace App\Http\Controllers;

use App\Http\Requests\Doctor\StoreDoctorRequest;
use App\Http\Requests\Doctor\UpdateDoctorRequest;
use App\Models\Doctor;
use App\Models\Especialidad;
use App\Models\Persona;
use App\Models\Rol;
use App\Models\Turno;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    public function index()
    {
        try {
            /** @var User|null $user */
            $user = Auth::user();
            if ($user->rol->nombre === 'Doctor') {
                $doctor = $user->persona->doctores;

                return redirect()->route('doctor.show', ['codigo_doctor' => $doctor->codigo_doctor]);
            }
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
                ])
                ->get();

            return Inertia::render('Doctor/Index', [
                'doctores' => $doctores,
            ]);

        } catch (\Exception $e) {
            return Inertia::render('Doctor/Index', [
                'doctores' => [],
                'error' => 'Error al obtener los doctores: '.$e->getMessage(),
            ]);
        }
    }

    public function store(StoreDoctorRequest $request)
    {
        $data = $request->validated();
        try {
            $rolDoctor = Rol::where('nombre', 'Doctor')->firstOrFail();
            DB::beginTransaction();
            // vamos a verificar si ya existe un propietario con el mismo carnet de identidad
            $doctorExistente = Doctor::where('persona_id', $data['carnet_identidad'])->first();
            if ($doctorExistente) {
                DB::rollBack();

                return redirect()
                    ->route('doctor.index')
                    ->with('error', 'Ya existe un doctor con el mismo carnet de identidad.')
                    ->with('flash_id', uniqid());
            }
            $personaExistente = Persona::where('carnet_identidad', $data['carnet_identidad'])->first();
            if (! $personaExistente) {
                $datosPersona = collect($data)->only([
                    'carnet_identidad',
                    'nombre',
                    'apellido',
                    'fecha_nacimiento',
                    'direccion',
                    'genero',
                    'telefono',
                ])->toArray();
                $personaExistente = Persona::create($datosPersona);
            }

            $codigoDoctor = 'DOC-'.$personaExistente->carnet_identidad;
            $datosDoctor = collect($data)->only([
                'matricula_profesional',
                'experiencia',
                'telefono_profesional',
                'fecha_contratacion',
            ])->toArray();

            $datosDoctor['codigo_doctor'] = $codigoDoctor;
            $datosDoctor['persona_id'] = $personaExistente->carnet_identidad;

            $doctor = Doctor::create($datosDoctor);

            $datosUsuario = collect($data)->only([
                'email',
                'url',
                'password',
                'estado',
            ])->toArray();
            $datosUsuario['password'] = Hash::make($datosUsuario['password']);
            $datosUsuario['persona_id'] = $personaExistente->carnet_identidad;
            $datosUsuario['rol_id'] = $rolDoctor->id;
            $codigoUsuario = 'USR-'.$personaExistente->carnet_identidad;
            $datosUsuario['codigo_usuario'] = $codigoUsuario;
            $usuario = User::create($datosUsuario);

            DB::commit();

            return redirect()
                ->route('doctor.index')
                ->with('success', 'Doctor creado exitosamente.')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('doctor.index')
                ->with('error', 'Error al crear el doctor: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function show(string $codigo_doctor)
    {
        try {
            $doctor = Doctor::where('codigo_doctor', $codigo_doctor)
                ->with([
                    'persona:carnet_identidad,nombre,apellido,telefono,fecha_nacimiento,direccion,genero',
                    'persona.usuarios:codigo_usuario,estado,url,email,persona_id,rol_id',
                    'persona.usuarios.rol:id,nombre',
                    'especialidades:id,nombre,descripcion',
                    'turnos:id,nombre,estado,hora_inicio,hora_fin',
                ])
                ->firstOrFail();

            $especialidades = Especialidad::all();
            $turnos = Turno::all();

            return Inertia::render('Doctor/Show', [
                'doctor' => $doctor,
                'especialidades' => $especialidades,
                'turnos' => $turnos,
            ]);

        } catch (\Exception $e) {
            return redirect()
                ->route('doctor.index')
                ->with('error', 'Error al obtener el doctor: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function update(UpdateDoctorRequest $request, string $codigo_doctor)
    {

        $data = $request->validated();
        try {
            DB::beginTransaction();
            $doctor = Doctor::findOrFail($codigo_doctor);
            $datoDoctor = collect($data)->only([
                'fecha_inicio',
                'porcentaje_participacion',
            ])->toArray();
            $doctor->update($datoDoctor);

            $persona = Persona::findOrFail($doctor->persona_id);
            $datoPersona = collect($data)->only([
                'nombre',
                'apellido',
                'fecha_nacimiento',
                'direccion',
                'genero',
                'telefono',
            ])->toArray();
            $persona->update($datoPersona);

            $usuario = User::where('persona_id', $persona->carnet_identidad)->firstOrFail();
            $datoUsuario = collect($data)->only([
                'email',
                'estado',
            ])->toArray();

            // if (! empty($data['password'])) {
            //     $datoUsuario['password'] = Hash::make($data['password']);
            // }
            if ($request->hasFile('foto_perfil')) {
                if ($usuario && $usuario->url) {
                    $rutaAnterior = str_replace('/storage/', '', $usuario->url);

                    if (Storage::disk('public')->exists($rutaAnterior)) {
                        Storage::disk('public')->delete($rutaAnterior);
                    }
                }

                $ruta = $request->file('foto_perfil')->store('perfiles', 'public');

                $datoUsuario['url'] = Storage::url($ruta);
            }
            $usuario->update($datoUsuario);
            DB::commit();

            return redirect()
                ->route('doctor.index')
                ->with('success', 'Doctor actualizado exitosamente.')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('doctor.index')
                ->with('error', 'Error al actualizar el doctor: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function asignarEspecialidad(Request $request, string $codigo_doctor)
    {
        $request->validate([
            'especialidades' => ['nullable', 'array'],
            'especialidades.*' => ['exists:especialidades,id'],
        ]);

        try {
            $doctor = Doctor::findOrFail($codigo_doctor);

            $doctor->especialidades()->sync($request->especialidades ?? []);

            return redirect()
                ->route('doctor.show', ['codigo_doctor' => $codigo_doctor])
                ->with('success', 'Especialidades actualizadas exitosamente.')
                ->with('flash_id', uniqid());

        } catch (\Exception $e) {
            return redirect()
                ->route('doctor.show', ['codigo_doctor' => $codigo_doctor])
                ->with('error', 'Error al actualizar especialidades: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function asignarTurno(Request $request, string $codigo_doctor)
    {
        $request->validate([
            'turnos' => ['nullable', 'array'],
            'turnos.*.id' => ['required', 'exists:turnos,id'],
            'turnos.*.dia_semana' => [
                'required',
                'string',
                'in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
            ],
        ]);

        try {
            DB::beginTransaction();

            $doctor = Doctor::findOrFail($codigo_doctor);

            // Elimina todos los turnos actuales del doctor
            $doctor->turnos()->detach();

            // Inserta nuevamente los turnos enviados
            foreach ($request->turnos ?? [] as $turno) {
                $doctor->turnos()->attach($turno['id'], [
                    'dia_semana' => $turno['dia_semana'],
                ]);
            }

            DB::commit();

            return redirect()
                ->route('doctor.show', ['codigo_doctor' => $codigo_doctor])
                ->with('success', 'Turnos actualizados exitosamente.')
                ->with('flash_id', uniqid());

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('doctor.show', ['codigo_doctor' => $codigo_doctor])
                ->with('error', 'Error al actualizar turnos: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}
