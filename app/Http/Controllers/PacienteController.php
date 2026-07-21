<?php

namespace App\Http\Controllers;

use App\Http\Requests\Paciente\StorePacienteRequest;
use App\Http\Requests\Paciente\UpdatePacienteRequest;
use App\Models\Paciente;
use App\Models\Persona;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PacienteController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            if ($user->rol->nombre === 'Paciente') {
                $paciente = $user->persona->pacientes;

                return redirect()->route('paciente.show', ['codigo_paciente' => $paciente->codigo_paciente]);
            }
            $pacientes = Paciente::select(
                'codigo_paciente',
                'persona_id',
                'contacto_emergencia',
                'telefono_emergencia'
            )
                ->with([
                    'persona:carnet_identidad,nombre,apellido,telefono,fecha_nacimiento,direccion,genero',
                    'persona.usuarios:codigo_usuario,email,url,estado,persona_id,rol_id',
                    'persona.usuarios.rol:id,nombre',
                ])
                ->get();

            return Inertia::render('Paciente/Index', [
                'pacientes' => $pacientes,
            ]);

        } catch (\Exception $e) {
            return Inertia::render('Paciente/Index', [
                'pacientes' => [],
                'error' => 'Error al obtener los pacientes: '.$e->getMessage(),
            ]);
        }
    }

    public function store(StorePacienteRequest $request)
    {
        $data = $request->validated();
        try {
            $rolPaciente = Rol::where('nombre', 'Paciente')->firstOrFail();
            DB::beginTransaction();
            // vamos a verificar si ya existe un propietario con el mismo carnet de identidad
            $pacienteExistente = Paciente::where('persona_id', $data['carnet_identidad'])->first();
            if ($pacienteExistente) {
                DB::rollBack();

                return redirect()
                    ->route('paciente.index')
                    ->with('error', 'Ya existe un paciente con el mismo carnet de identidad.')
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

            $codigoPaciente = 'PAC-'.$personaExistente->carnet_identidad;
            $datosPaciente = collect($data)->only([
                'contacto_emergencia',
                'telefono_emergencia',
            ])->toArray();

            $datosPaciente['codigo_paciente'] = $codigoPaciente;
            $datosPaciente['persona_id'] = $personaExistente->carnet_identidad;

            $paciente = Paciente::create($datosPaciente);

            $datosUsuario = collect($data)->only([
                'email',
                'url',
                'password',
                'estado',
            ])->toArray();
            $datosUsuario['password'] = Hash::make($datosUsuario['password']);
            $datosUsuario['persona_id'] = $personaExistente->carnet_identidad;
            $datosUsuario['rol_id'] = $rolPaciente->id;
            $codigoUsuario = 'USR-'.$personaExistente->carnet_identidad;
            $datosUsuario['codigo_usuario'] = $codigoUsuario;
            $usuario = User::create($datosUsuario);

            DB::commit();

            return redirect()
                ->route('paciente.index')
                ->with('success', 'Paciente creado exitosamente.')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('paciente.index')
                ->with('error', 'Error al crear el paciente: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function show(string $codigo_paciente)
    {
        try {
            $paciente = Paciente::where('codigo_paciente', $codigo_paciente)
                ->with([
                    'persona:carnet_identidad,nombre,apellido,telefono,fecha_nacimiento,direccion,genero',
                    'persona.usuarios:codigo_usuario,estado,url,email,persona_id,rol_id',
                    'persona.usuarios.rol:id,nombre',
                ])
                ->firstOrFail();

            return Inertia::render('Paciente/Show', [
                'paciente' => $paciente,
            ]);
        } catch (\Exception $e) {
            return redirect()
                ->route('paciente.index')
                ->with('error', 'Error al obtener el paciente: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }

    }

    public function update(UpdatePacienteRequest $request, string $codigo_paciente)
    {

        $data = $request->validated();
        try {
            DB::beginTransaction();
            $paciente = Paciente::findOrFail($codigo_paciente);
            $datoPaciente = collect($data)->only([
                'fecha_inicio',
                'porcentaje_participacion',
            ])->toArray();
            $paciente->update($datoPaciente);

            $persona = Persona::findOrFail($paciente->persona_id);
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
                ->route('paciente.index')
                ->with('success', 'Paciente actualizado exitosamente.')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('paciente.index')
                ->with('error', 'Error al actualizar el paciente: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}
