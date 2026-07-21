<?php

namespace App\Http\Controllers;

use App\Http\Requests\Rol\StoreRolRequest;
use App\Http\Requests\Rol\UpdateRolRequest;
use App\Models\Modulo;
use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RolController extends Controller
{
    public function index()
    {
        try {
            $roles = Rol::all();

            return Inertia::render('Rol/Index', [
                'roles' => $roles,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Rol/Index', [
                'roles' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function store(StoreRolRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $rol = Rol::create($data);
            DB::commit();

            return redirect()->route('rol.index')
                ->with('success', 'Rol creado exitosamente')
                ->with('flash_id', uniqid());

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('rol.index')
                ->with('error', 'Error al crear el rol: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function show(int $rol_id)
    {
        try {
            $modulos = Modulo::select([
                'id',
                'nombre',
                'estado',
            ])
                ->get();
                
            $permisos = Permiso::select([
                'id',
                'nombre',
                'estado',
                'modulo_id',
            ])
                ->with([
                    'modulo:id,nombre,estado',
                ])
                ->get();
            $rol = Rol::select([
                'id',
                'nombre',
                'description',
                'estado',
            ])
                ->with([
                    'permisos:id,nombre,estado,modulo_id',
                    'permisos.modulo:id,nombre,estado',
                ])
                ->findOrFail($rol_id);

            return Inertia::render('Rol/Show', [
                'rolPermisos' => $rol,
                'permisos' => $permisos,
                'modulos' => $modulos,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Rol/Show', [
                'rolPermisos' => null,
                'permisos' => [],
                'modulos' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function update(UpdateRolRequest $request, int $rol_id)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $rol = Rol::findOrFail($rol_id);
            $rol->update($data);
            DB::commit();

            return redirect()->route('rol.index')
                ->with('success', 'Rol actualizado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('rol.index')
                ->with('error', 'Error al actualizar el rol: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function destroy(int $rol_id)
    {
        DB::beginTransaction();
        try {
            $rol = Rol::findOrFail($rol_id);
            $rol->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rol eliminado exitosamente',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el rol',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function asignarPermiso(Request $request,int $rol_id)
    {
        DB::beginTransaction();
        try {
            $rol = Rol::findOrFail($rol_id);
            $rol->permisos()->attach($request->input('permiso_id')); // esto reemplaza los anteriores permisos asignados con los nuevos permisos proporcionados
            DB::commit();

            return redirect()->route('rol.show', ['rol_id' => $rol_id])
                ->with('success', 'Permisos asignados exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('rol.show', ['rol_id' => $rol_id])
                ->with('error', 'Error al asignar permisos: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function eliminarPermiso(int $rol_id, int $permiso_id)
    {
        DB::beginTransaction();
        try {
            $rol = Rol::findOrFail($rol_id);
            $rol->permisos()->detach($permiso_id); // esto elimina la relación entre el rol y el permiso
            DB::commit();

            return redirect()->route('rol.show', ['rol_id' => $rol_id])
                ->with('success', 'Permiso eliminado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('rol.show', ['rol_id' => $rol_id])
                ->with('error', 'Error al eliminar el permiso: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}
