<?php

namespace App\Http\Controllers;

use App\Http\Requests\Administrador\StoreAdministradorRequest;
use App\Http\Requests\Administrador\UpdateAdministradorRequest;
use App\Models\Persona;
use App\Models\Propietario;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AdministradorController extends Controller
{
    public function index()
    {
        try {
            /** @var User|null $user */
            $user = Auth::user();
            if ($user->rol->nombre === 'Propietario') {
                $propietario = $user->persona->propietarios;

                return redirect()->route('administrador.show', ['codigo_propietario' => $propietario->codigo_propietario]);
            }
            $administradores = Propietario::select(
                'codigo_propietario',
                'persona_id',
                'fecha_inicio',
                'porcentaje_participacion'
            )
                ->with([
                    'persona:carnet_identidad,nombre,apellido,telefono,fecha_nacimiento,direccion,genero',
                    'persona.usuarios:codigo_usuario,email,url,estado,persona_id,rol_id',
                    'persona.usuarios.rol:id,nombre',
                ])
                ->get();

            return Inertia::render('Administrador/Index', [
                'propietarios' => $administradores,
            ]);

        } catch (\Exception $e) {
            return Inertia::render('Administrador/Index', [
                'propietarios' => [],
                'error' => 'Error al obtener los administradores: '.$e->getMessage(),
            ]);
        }
    }

    public function store(StoreAdministradorRequest $request)
    {
        $data = $request->validated();
        try {
            $rolAdministrador = Rol::where('nombre', 'Administrador')->firstOrFail();
            DB::beginTransaction();
            // vamos a verificar si ya existe un propietario con el mismo carnet de identidad
            $propietarioExistente = Propietario::where('persona_id', $data['carnet_identidad'])->first();
            if ($propietarioExistente) {
                DB::rollBack();

                return redirect()
                    ->route('administrador.index')
                    ->with('error', 'Ya existe un administrador con el mismo carnet de identidad.')
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

            $codigoPropietario = 'PRO-'.$personaExistente->carnet_identidad;
            $datosPropietario = collect($data)->only([
                'fecha_inicio',
                'porcentaje_participacion',
            ])->toArray();

            $datosPropietario['codigo_propietario'] = $codigoPropietario;
            $datosPropietario['persona_id'] = $personaExistente->carnet_identidad;

            $propietario = Propietario::create($datosPropietario);

            $datosUsuario = collect($data)->only([
                'email',
                'url',
                'password',
                'estado',
            ])->toArray();
            $datosUsuario['password'] = Hash::make($datosUsuario['password']);
            $datosUsuario['persona_id'] = $personaExistente->carnet_identidad;
            $datosUsuario['rol_id'] = $rolAdministrador->id;
            $codigoUsuario = 'USR-'.$personaExistente->carnet_identidad;
            $datosUsuario['codigo_usuario'] = $codigoUsuario;
            $usuario = User::create($datosUsuario);

            DB::commit();

            return redirect()
                ->route('administrador.index')
                ->with('success', 'Administrador creado exitosamente.')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('administrador.index')
                ->with('error', 'Error al crear el administrador: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function show(string $codigo_propietario)
    {
        try {
            $propietario = Propietario::where('codigo_propietario', $codigo_propietario)
                ->with([
                    'persona:carnet_identidad,nombre,apellido,telefono,fecha_nacimiento,direccion,genero',
                    'persona.usuarios:codigo_usuario,estado,url,email,persona_id,rol_id',
                    'persona.usuarios.rol:id,nombre',
                ])
                ->firstOrFail();

            return Inertia::render('Administrador/Show', [
                'administrador' => $propietario,
            ]);
        } catch (\Exception $e) {
            return redirect()
                ->route('administrador.index')
                ->with('error', 'Error al obtener el administrador: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }

    }

    public function update(UpdateAdministradorRequest $request, string $codigo_propietario)
    {

        $data = $request->validated();
        try {
            DB::beginTransaction();
            $propietario = Propietario::findOrFail($codigo_propietario);
            $datoPropietario = collect($data)->only([
                'fecha_inicio',
                'porcentaje_participacion',
            ])->toArray();
            $propietario->update($datoPropietario);

            $persona = Persona::findOrFail($propietario->persona_id);
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
                ->route('administrador.index')
                ->with('success', 'Administrador actualizado exitosamente.')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('administrador.index')
                ->with('error', 'Error al actualizar el administrador: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}
