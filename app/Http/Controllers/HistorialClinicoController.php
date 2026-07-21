<?php

namespace App\Http\Controllers;

use App\Http\Requests\HistorialClinico\StoreHistorialClinicoRequest;
use App\Http\Requests\HistorialClinico\UpdateHistorialClinicoRequest;
use App\Models\HistorialClinico;
use App\Models\Paciente;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use \App\Models\User;

class HistorialClinicoController extends Controller
{
    public function index()
    {
        try {
            /** @var User|null $user */
            $user = Auth::user();
            if ($user->rol->nombre === 'Paciente') {
                $paciente = $user->persona->pacientes;
                $historial = HistorialClinico::where('paciente_ci', $paciente->codigo_paciente)
                    ->first();

                if ($historial) {
                    return redirect()->route('historial_clinico.show', ['codigo_historial' => $historial->codigo_historial]);
                }else if ($historial === null) {
                    return back();
                }
            }
            $historiales = HistorialClinico::activo()
                ->with(['paciente.persona', 'antecedentesOdontologicos.detalleAntecedentes'])
                ->get();

            // Get patients who don't have a clinical history yet
            $pacientesDisponibles = Paciente::with('persona')
                ->whereNotIn('codigo_paciente', function ($query) {
                    $query->select('paciente_ci')->from('historiales_clinicos');
                })
                ->get();

            $pacientesTodos = Paciente::with('persona')->get();

            return Inertia::render('HistorialClinico/Index', [
                'historiales' => $historiales,
                'pacientesDisponibles' => $pacientesDisponibles,
                'pacientesTodos' => $pacientesTodos,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('HistorialClinico/Index', [
                'historiales' => [],
                'pacientesDisponibles' => [],
                'pacientesTodos' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function store(StoreHistorialClinicoRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $historial = HistorialClinico::create($data);

            if (isset($data['antecedentes_odontologicos'])) {
                $antecedenteData = $data['antecedentes_odontologicos'];
                $antecedente = $historial->antecedentesOdontologicos()->create([
                    'fecha_registro' => $antecedenteData['fecha_registro'],
                    'observacion_general' => $antecedenteData['observacion_general'] ?? null,
                ]);

                if (isset($antecedenteData['detalles'])) {
                    foreach ($antecedenteData['detalles'] as $detalle) {
                        $antecedente->detalleAntecedentes()->create($detalle);
                    }
                }
            }

            DB::commit();

            return redirect()->route('historial_clinico.index')
                ->with('success', 'Historial clínico creado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('historial_clinico.index')
                ->with('error', 'Error al crear el historial clínico: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function show(int $codigo_historial)
    {
        try {
            $historial = HistorialClinico::with([
                'paciente.persona',
                'antecedentesOdontologicos.detalleAntecedentes',
                'citas.doctor.persona',
                'citas.diagnostico',
                'tratamiento',
            ])->findOrFail($codigo_historial);

            return Inertia::render('HistorialClinico/Show', [
                'historial' => $historial,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('historial_clinico.index')
                ->with('error', 'Error al cargar el historial clínico: '.$e->getMessage());
        }
    }

    public function update(UpdateHistorialClinicoRequest $request, int $codigo_historial)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $historial = HistorialClinico::findOrFail($codigo_historial);
            $historial->update($data);

            if (isset($data['antecedentes_odontologicos'])) {
                $antecedenteData = $data['antecedentes_odontologicos'];

                // Update or Create Antecedent
                $antecedente = $historial->antecedentesOdontologicos;
                if ($antecedente) {
                    $antecedente->update([
                        'fecha_registro' => $antecedenteData['fecha_registro'],
                        'observacion_general' => $antecedenteData['observacion_general'] ?? null,
                    ]);
                } else {
                    $antecedente = $historial->antecedentesOdontologicos()->create([
                        'fecha_registro' => $antecedenteData['fecha_registro'],
                        'observacion_general' => $antecedenteData['observacion_general'] ?? null,
                    ]);
                }

                // Details synchronization
                if (isset($antecedenteData['detalles'])) {
                    $updatedIds = collect($antecedenteData['detalles'])->pluck('id_detalle_antecedente')->filter()->toArray();
                    $antecedente->detalleAntecedentes()->whereNotIn('id_detalle_antecedente', $updatedIds)->delete();

                    foreach ($antecedenteData['detalles'] as $detalleData) {
                        if (isset($detalleData['id_detalle_antecedente'])) {
                            $detalle = $antecedente->detalleAntecedentes()->findOrFail($detalleData['id_detalle_antecedente']);
                            $detalle->update($detalleData);
                        } else {
                            $antecedente->detalleAntecedentes()->create($detalleData);
                        }
                    }
                } else {
                    $antecedente->detalleAntecedentes()->delete();
                }
            }

            DB::commit();

            return redirect()->route('historial_clinico.index')
                ->with('success', 'Historial clínico actualizado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('historial_clinico.index')
                ->with('error', 'Error al actualizar el historial clínico: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function destroy(int $codigo_historial)
    {
        DB::beginTransaction();
        try {
            $historial = HistorialClinico::findOrFail($codigo_historial);
            $historial->update(['estado' => 'INACTIVO']);
            DB::commit();

            return redirect()->route('historial_clinico.index')
                ->with('success', 'Historial clínico inactivado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('historial_clinico.index')
                ->with('error', 'Error al inactivar el historial clínico: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}
