<?php

namespace App\Http\Controllers;

use App\Http\Requests\Modulo\StoreModuloRequest;
use App\Http\Requests\Modulo\UpdateModuloRequest;
use App\Models\Modulo;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ModuloController extends Controller
{
    public function index()
    {
        try {
            $modulos = Modulo::all();

            return Inertia::render('Modulo/Index', [
                'modulos' => $modulos,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Modulo/Index', [
                'modulos' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function store(StoreModuloRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $modulo = Modulo::create($data);
            DB::commit();

            // vamos a devolver el modulo creado en la respuesta
            return redirect()->route('modulo.index')
                ->with('success', 'Módulo creado exitosamente')
                ->with('flash_id', uniqid());

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('modulo.index')
                ->with('error', 'Error al crear el módulo: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function show(int $modulo_id)
    {
        try {
            $modulo = Modulo::findOrFail($modulo_id);

            return response()->json([
                'success' => true,
                'message' => 'Módulo obtenido exitosamente',
                'data' => $modulo,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el módulo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateModuloRequest $request, int $modulo_id)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $modulo = Modulo::findOrFail($modulo_id);
            $modulo->update($data);
            DB::commit();

            return redirect()->route('modulo.index')
                ->with('success', 'Módulo actualizado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('modulo.index')
                ->with('error', 'Error al actualizar el módulo: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function destroy(int $modulo_id)
    {
        DB::beginTransaction();
        try {

            $modulo = Modulo::findOrFail($modulo_id);
            $permisosCount = $modulo->permisos()->count();
            if ($permisosCount > 0) {
                DB::rollBack();

                return redirect()->route('modulo.index')
                    ->with('error', 'No se puede eliminar el módulo porque tiene permisos asociados')
                    ->with('flash_id', uniqid());
            }
            $modulo->delete();
            DB::commit();

            return redirect()->route('modulo.index')
                ->with('success', 'Módulo eliminado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('modulo.index')
                ->with('error', 'Error al eliminar el módulo: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}
