<?php

namespace App\Http\Controllers;

use App\Http\Requests\AntecedentesOdontologicos\StoreAntecedentesOdontologicosRequest;
use App\Http\Requests\AntecedentesOdontologicos\UpdateAntecedentesOdontologicosRequest;
use App\Models\AntecedentesOdontologicos;
use App\Models\HistorialClinico;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AntecedentesOdontologicosController extends Controller
{
    public function index()
    {
        try {
            $antecedentes = AntecedentesOdontologicos::with(['detalleAntecedentes', 'historialClinico.paciente.persona'])->get();
            $historiales = HistorialClinico::activo()->with('paciente.persona')->get();

            return Inertia::render('AntecedentesOdontologicos/Index', [
                'antecedentes' => $antecedentes,
                'historiales' => $historiales,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('AntecedentesOdontologicos/Index', [
                'antecedentes' => [],
                'historiales' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function store(StoreAntecedentesOdontologicosRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $antecedente = AntecedentesOdontologicos::create([
                'observacion_general' => $data['observacion_general'] ?? null,
                'fecha_registro' => $data['fecha_registro'],
                'codigo_historial' => $data['codigo_historial'],
            ]);

            if (isset($data['detalles'])) {
                foreach ($data['detalles'] as $detalle) {
                    $antecedente->detalleAntecedentes()->create($detalle);
                }
            }

            DB::commit();

            return redirect()->route('antecedentes_odontologicos.index')
                ->with('success', 'Antecedente odontológico registrado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('antecedentes_odontologicos.index')
                ->with('error', 'Error al registrar el antecedente: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function show(int $id)
    {
        try {
            $antecedente = AntecedentesOdontologicos::with(['detalleAntecedentes', 'historialClinico.paciente.persona'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $antecedente,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateAntecedentesOdontologicosRequest $request, int $id)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $antecedente = AntecedentesOdontologicos::findOrFail($id);
            
            $antecedente->update([
                'observacion_general' => array_key_exists('observacion_general', $data) ? $data['observacion_general'] : $antecedente->observacion_general,
                'fecha_registro' => $data['fecha_registro'] ?? $antecedente->fecha_registro,
                'codigo_historial' => $data['codigo_historial'] ?? $antecedente->codigo_historial,
            ]);

            if (isset($data['detalles'])) {
                // Delete details that are not in the update request
                $updatedIds = collect($data['detalles'])->pluck('id_detalle_antecedente')->filter()->toArray();
                $antecedente->detalleAntecedentes()->whereNotIn('id_detalle_antecedente', $updatedIds)->delete();

                foreach ($data['detalles'] as $detalleData) {
                    if (isset($detalleData['id_detalle_antecedente'])) {
                        $detalle = $antecedente->detalleAntecedentes()->findOrFail($detalleData['id_detalle_antecedente']);
                        $detalle->update($detalleData);
                    } else {
                        $antecedente->detalleAntecedentes()->create($detalleData);
                    }
                }
            }

            DB::commit();

            return redirect()->route('antecedentes_odontologicos.index')
                ->with('success', 'Antecedente odontológico actualizado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('antecedentes_odontologicos.index')
                ->with('error', 'Error al actualizar el antecedente: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();
        try {
            $antecedente = AntecedentesOdontologicos::findOrFail($id);
            $antecedente->delete();
            DB::commit();

            return redirect()->route('antecedentes_odontologicos.index')
                ->with('success', 'Antecedente odontológico eliminado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('antecedentes_odontologicos.index')
                ->with('error', 'Error al eliminar el antecedente: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}
