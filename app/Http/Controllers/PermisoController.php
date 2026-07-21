<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permiso\StorePermisoRequest;
use App\Http\Requests\Permiso\UpdatePermisoRequest;
use App\Models\Modulo;
use App\Models\Permiso;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PermisoController extends Controller
{
    public function index()
    {
        try {
            $permisos = Permiso::all();
            $modulos = Modulo::all();

            return Inertia::render('Permiso/Index', [
                'permisos' => $permisos,
                'modulos' => $modulos,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Permiso/Index', [
                'permisos' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function store(StorePermisoRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $permiso = Permiso::create($data);
            DB::commit();

            return redirect()->route('permiso.index')
                ->with('success', 'Permiso creado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('permiso.index')
                ->with('error', 'Error al crear el permiso: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function show(int $permiso_id)
    {
        try {
            $permiso = Permiso::findOrFail($permiso_id);

            return response()->json([
                'success' => true,
                'message' => 'Permiso obtenido exitosamente',
                'data' => $permiso,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el permiso',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdatePermisoRequest $request, int $permiso_id)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $permiso = Permiso::findOrFail($permiso_id);
            $permiso->update($data);
            DB::commit();

            return redirect()->route('permiso.index')
                ->with('success', 'Permiso actualizado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('permiso.index')
                ->with('error', 'Error al actualizar el permiso: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function destroy(int $permiso_id)
    {
        DB::beginTransaction();
        try {
            $permiso = Permiso::findOrFail($permiso_id);
            $rolesCount = $permiso->roles()->count();
            if ($rolesCount > 0) {
                DB::rollBack();
                return redirect()->route('permiso.index')
                    ->with('error', 'No se puede eliminar el permiso porque está asociado a uno o más roles.')
                    ->with('flash_id', uniqid());
            }
            $permiso->delete();
            DB::commit();

            return redirect()->route('permiso.index')
                ->with('success', 'Permiso eliminado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('permiso.index')
                ->with('error', 'Error al eliminar el permiso: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}
