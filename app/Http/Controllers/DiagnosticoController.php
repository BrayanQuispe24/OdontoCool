<?php

namespace App\Http\Controllers;

use App\Http\Requests\Diagnostico\StoreDiagnosticoRequest;
use App\Http\Requests\Diagnostico\UpdateDiagnosticoRequest;
use App\Models\Diagnostico;
use App\Models\Cita;
use App\Models\Diente;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DiagnosticoController extends Controller
{
    public function index()
    {
        try {
            $diagnosticos = Diagnostico::with(['cita.paciente.persona', 'detalleDiagnostico.diente'])->get();
            
            // Get appointments that don't have a diagnosis yet
            $citasDisponibles = Cita::with('paciente.persona')
                ->whereNotIn('id_cita', function ($query) {
                    $query->select('cita_id')->from('diagnosticos');
                })
                ->get();

            $citasTodas = Cita::with('paciente.persona')->get();
            $dientes = Diente::activo()->get();

            return Inertia::render('Diagnostico/Index', [
                'diagnosticos' => $diagnosticos,
                'citasDisponibles' => $citasDisponibles,
                'citasTodas' => $citasTodas,
                'dientes' => $dientes,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Diagnostico/Index', [
                'diagnosticos' => [],
                'citasDisponibles' => [],
                'citasTodas' => [],
                'dientes' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function store(StoreDiagnosticoRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $diagnostico = Diagnostico::create([
                'sintomas' => $data['sintomas'],
                'tipo_diagnostico' => $data['tipo_diagnostico'],
                'gravedad' => $data['gravedad'],
                'observaciones' => $data['observaciones'] ?? null,
                'cita_id' => $data['cita_id'],
            ]);

            if (isset($data['detalles'])) {
                foreach ($data['detalles'] as $detalle) {
                    $diagnostico->detalleDiagnostico()->create([
                        'observacion' => $detalle['observacion'] ?? null,
                        'zona_bucal' => $detalle['zona_bucal'],
                        'diente_id' => $detalle['diente_id'],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('diagnostico.index')
                ->with('success', 'Diagnóstico registrado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('diagnostico.index')
                ->with('error', 'Error al registrar el diagnóstico: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function show(int $id)
    {
        try {
            $diagnostico = Diagnostico::with(['cita.paciente.persona', 'detalleDiagnostico.diente', 'tratamiento'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $diagnostico,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateDiagnosticoRequest $request, int $id)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $diagnostico = Diagnostico::findOrFail($id);
            
            $diagnostico->update([
                'sintomas' => $data['sintomas'] ?? $diagnostico->sintomas,
                'tipo_diagnostico' => $data['tipo_diagnostico'] ?? $diagnostico->tipo_diagnostico,
                'gravedad' => $data['gravedad'] ?? $diagnostico->gravedad,
                'observaciones' => array_key_exists('observaciones', $data) ? $data['observaciones'] : $diagnostico->observaciones,
                'cita_id' => $data['cita_id'] ?? $diagnostico->cita_id,
            ]);

            if (isset($data['detalles'])) {
                $updatedIds = collect($data['detalles'])->pluck('id')->filter()->toArray();
                $diagnostico->detalleDiagnostico()->whereNotIn('id', $updatedIds)->delete();

                foreach ($data['detalles'] as $detalleData) {
                    if (isset($detalleData['id'])) {
                        $detalle = $diagnostico->detalleDiagnostico()->findOrFail($detalleData['id']);
                        $detalle->update([
                            'observacion' => $detalleData['observacion'] ?? null,
                            'zona_bucal' => $detalleData['zona_bucal'],
                            'diente_id' => $detalleData['diente_id'],
                        ]);
                    } else {
                        $diagnostico->detalleDiagnostico()->create([
                            'observacion' => $detalleData['observacion'] ?? null,
                            'zona_bucal' => $detalleData['zona_bucal'],
                            'diente_id' => $detalleData['diente_id'],
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('diagnostico.index')
                ->with('success', 'Diagnóstico actualizado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('diagnostico.index')
                ->with('error', 'Error al actualizar el diagnóstico: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();
        try {
            $diagnostico = Diagnostico::findOrFail($id);
            $diagnostico->delete();
            DB::commit();

            return redirect()->route('diagnostico.index')
                ->with('success', 'Diagnóstico eliminado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('diagnostico.index')
                ->with('error', 'Error al eliminar el diagnóstico: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}
