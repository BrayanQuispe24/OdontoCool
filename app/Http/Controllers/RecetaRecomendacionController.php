<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecetaRecomendacion\StoreRecetaRecomendacionRequest;
use App\Http\Requests\RecetaRecomendacion\UpdateRecetaRecomendacionRequest;
use App\Models\RecetaRecomendacion;
use App\Models\DetalleRecomendacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RecetaRecomendacionController extends Controller
{
    public function index()
    {
        try {
            $recetas = RecetaRecomendacion::with('detalles')->get();

            return Inertia::render('RecetaRecomendacion/Index', [
                'recetas' => $recetas,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('RecetaRecomendacion/Index', [
                'recetas' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function store(StoreRecetaRecomendacionRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $receta = RecetaRecomendacion::create([
                'fecha_emision' => $data['fecha_emision'],
                'observacion_general' => $data['observacion_general'] ?? null,
                'tratamiento_id' => $data['tratamiento_id'],
            ]);

            foreach ($data['detalles'] as $detalle) {
                $receta->detalles()->create($detalle);
            }

            DB::commit();

            return redirect()->route('receta_recomendacion.index')
                ->with('success', 'Receta y recomendaciones creadas exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('receta_recomendacion.index')
                ->with('error', 'Error al crear la receta: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function show(int $receta_id)
    {
        try {
            $receta = RecetaRecomendacion::with('detalles')->findOrFail($receta_id);

            return Inertia::render('RecetaRecomendacion/Show', [
                'receta' => $receta,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('RecetaRecomendacion/Show', [
                'receta' => null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function update(UpdateRecetaRecomendacionRequest $request, int $receta_id)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $receta = RecetaRecomendacion::findOrFail($receta_id);
            
            $receta->update([
                'fecha_emision' => $data['fecha_emision'] ?? $receta->fecha_emision,
                'observacion_general' => array_key_exists('observacion_general', $data) ? $data['observacion_general'] : $receta->observacion_general,
                'tratamiento_id' => $data['tratamiento_id'] ?? $receta->tratamiento_id,
            ]);

            if (isset($data['detalles'])) {
                // Delete details that are not in the update request
                $updatedIds = collect($data['detalles'])->pluck('id')->filter()->toArray();
                $receta->detalles()->whereNotIn('id', $updatedIds)->delete();

                foreach ($data['detalles'] as $detalleData) {
                    if (isset($detalleData['id'])) {
                        $detalle = $receta->detalles()->findOrFail($detalleData['id']);
                        $detalle->update($detalleData);
                    } else {
                        $receta->detalles()->create($detalleData);
                    }
                }
            }

            DB::commit();

            return redirect()->route('receta_recomendacion.index')
                ->with('success', 'Receta y recomendaciones actualizadas exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('receta_recomendacion.index')
                ->with('error', 'Error al actualizar la receta: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function destroy(int $receta_id)
    {
        DB::beginTransaction();
        try {
            $receta = RecetaRecomendacion::findOrFail($receta_id);
            $receta->delete();
            DB::commit();

            return redirect()->route('receta_recomendacion.index')
                ->with('success', 'Receta eliminada exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('receta_recomendacion.index')
                ->with('error', 'Error al eliminar la receta: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}
