<?php

namespace App\Http\Controllers;

use App\Http\Requests\Secretaria\StoreSecretariaRequest;
use App\Http\Requests\Secretaria\UpdateSecretariaRequest;
use App\Models\Persona;
use App\Models\Rol;
use App\Models\Secretaria;
use App\Models\Turno;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class SecretariaController extends Controller
{
    public function index()
    {
        try {
            /** @var User|null $user */
            $user = Auth::user();
            if ($user->rol->nombre === 'Secretaria') {
                $secretaria = $user->persona->secretarias;

                return redirect()
                    ->route('secretaria.show', ['codigo_secretaria' => $secretaria->codigo_secretaria]);
            }
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

            return Inertia::render('Secretaria/Index', [
                'secretarias' => $secretarias,
            ]);

        } catch (\Exception $e) {
            return Inertia::render('Secretaria/Index', [
                'secretarias' => [],
                'error' => 'Error al obtener las secretarias: '.$e->getMessage(),
            ]);
        }
    }

    public function store(StoreSecretariaRequest $request)
    {
        $data = $request->validated();
        try {
            $rolSecretaria = Rol::where('nombre', 'Secretaria')->firstOrFail();
            DB::beginTransaction();
            // vamos a verificar si ya existe un propietario con el mismo carnet de identidad
            $secretariaExistente = Secretaria::where('persona_id', $data['carnet_identidad'])->first();
            if ($secretariaExistente) {
                DB::rollBack();

                return redirect()
                    ->route('secretaria.index')
                    ->with('error', 'Ya existe una secretaria con el mismo carnet de identidad.')
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

            $codigoSecretaria = 'SEC-'.$personaExistente->carnet_identidad;
            $datosSecretaria = collect($data)->only([
                'fecha_contratacion',
            ])->toArray();

            $datosSecretaria['codigo_secretaria'] = $codigoSecretaria;
            $datosSecretaria['persona_id'] = $personaExistente->carnet_identidad;

            $secretaria = Secretaria::create($datosSecretaria);

            $datosUsuario = collect($data)->only([
                'email',
                'url',
                'password',
                'estado',
            ])->toArray();
            $datosUsuario['password'] = Hash::make($datosUsuario['password']);
            $datosUsuario['persona_id'] = $personaExistente->carnet_identidad;
            $datosUsuario['rol_id'] = $rolSecretaria->id;
            $codigoUsuario = 'USR-'.$personaExistente->carnet_identidad;
            $datosUsuario['codigo_usuario'] = $codigoUsuario;
            $usuario = User::create($datosUsuario);

            DB::commit();

            return redirect()
                ->route('secretaria.index')
                ->with('success', 'Secretaria creada exitosamente.')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('secretaria.index')
                ->with('error', 'Error al crear la secretaria: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function show(string $codigo_secretaria)
    {
        try {
            $secretaria = Secretaria::where('codigo_secretaria', $codigo_secretaria)
                ->with([
                    'persona:carnet_identidad,nombre,apellido,telefono,fecha_nacimiento,direccion,genero',
                    'persona.usuarios:codigo_usuario,estado,url,email,persona_id,rol_id',
                    'persona.usuarios.rol:id,nombre',
                    'turnos:id,nombre,estado,hora_inicio,hora_fin',
                ])
                ->firstOrFail();
            $turnos = Turno::all();

            return Inertia::render('Secretaria/Show', [
                'secretaria' => $secretaria,
                'turnos' => $turnos,
            ]);
        } catch (\Exception $e) {
            return redirect()
                ->route('secretaria.index')
                ->with('error', 'Error al obtener la secretaria: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }

    }

    public function update(UpdateSecretariaRequest $request, string $codigo_secretaria)
    {

        $data = $request->validated();
        try {
            DB::beginTransaction();
            $secretaria = Secretaria::findOrFail($codigo_secretaria);
            $datoSecretaria = collect($data)->only([
                'fecha_contratacion',
            ])->toArray();
            $secretaria->update($datoSecretaria);

            $persona = Persona::findOrFail($secretaria->persona_id);
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
                ->route('secretaria.index')
                ->with('success', 'Secretaria actualizada exitosamente.')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('secretaria.index')
                ->with('error', 'Error al actualizar la secretaria: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function asignarTurno(Request $request, string $codigo_secretaria)
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

            $secretaria = Secretaria::findOrFail($codigo_secretaria);

            // Elimina todos los turnos actuales de la secretaria
            $secretaria->turnos()->detach();

            // Inserta nuevamente los turnos enviados
            foreach ($request->turnos ?? [] as $turno) {
                $secretaria->turnos()->attach($turno['id'], [
                    'dia_semana' => $turno['dia_semana'],
                ]);
            }

            DB::commit();

            return redirect()
                ->route('secretaria.show', ['codigo_secretaria' => $codigo_secretaria])
                ->with('success', 'Turnos actualizados exitosamente.')
                ->with('flash_id', uniqid());

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('secretaria.show', ['codigo_secretaria' => $codigo_secretaria])
                ->with('error', 'Error al actualizar turnos: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}
