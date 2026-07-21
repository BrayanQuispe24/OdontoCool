<?php

namespace App\Http\Controllers;

use App\Http\Requests\Analisis\StoreAnalisisRequest;
use App\Http\Requests\Analisis\UpdateAnalisisRequest;
use App\Models\Analisis;
use App\Models\Doctor;
use App\Models\SolicitudAnalisis;
use App\Models\Tratamiento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AnalisisController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();

            $solicitudes = collect();
            $tratamientos = collect();

            if ($user->rol?->nombre === 'Paciente') {
                $paciente = $user->persona?->pacientes;
                $historial = $paciente?->historialClinico;

                if ($historial) {
                    $tratamientos = $historial->tratamiento()
                        ->activo()
                        ->get();

                    $tratamientoIds = $tratamientos->pluck('id');

                    $solicitudes = SolicitudAnalisis::activo()
                        ->whereIn('tratamiento_id', $tratamientoIds)
                        ->with([
                            'analisis',
                            'tratamiento',
                            'doctor.persona',
                            'resultadoAnalisis' => function ($query) {
                                $query->activo();
                            },
                        ])
                        ->get();
                }
            } else {
                $solicitudes = SolicitudAnalisis::activo()
                    ->with([
                        'analisis',
                        'tratamiento',
                        'doctor.persona',
                        'resultadoAnalisis' => function ($query) {
                            $query->activo();
                        },
                    ])
                    ->get();

                $tratamientos = Tratamiento::activo()->get();
            }

            $analisis = Analisis::get();
            $doctores = Doctor::with('persona')->get();

            return Inertia::render('Analisis/Index', [
                'analisis' => $analisis,
                'solicitudes' => $solicitudes,
                'tratamientos' => $tratamientos,
                'doctores' => $doctores,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Analisis/Index', [
                'analisis' => [],
                'solicitudes' => [],
                'tratamientos' => [],
                'doctores' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function store(StoreAnalisisRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            Analisis::create($data);
            DB::commit();

            return redirect()->route('analisis.index')
                ->with('success', 'Análisis creado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('analisis.index')
                ->with('error', 'Error al crear el análisis: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function show(int $id)
    {
        try {
            $analisis = Analisis::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $analisis,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateAnalisisRequest $request, int $id)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $analisis = Analisis::findOrFail($id);
            $analisis->update($data);
            DB::commit();

            return redirect()->route('analisis.index')
                ->with('success', 'Análisis actualizado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('analisis.index')
                ->with('error', 'Error al actualizar el análisis: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();
        try {
            $analisis = Analisis::findOrFail($id);
            $analisis->update(['estado' => 'INACTIVO']);
            DB::commit();

            return redirect()->route('analisis.index')
                ->with('success', 'Análisis inactivado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('analisis.index')
                ->with('error', 'Error al inactivar el análisis: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function restore(int $id)
    {
        DB::beginTransaction();
        try {
            $analisis = Analisis::findOrFail($id);
            $analisis->update(['estado' => 'ACTIVO']);
            DB::commit();

            return redirect()->route('analisis.index')
                ->with('success', 'Análisis restaurado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('analisis.index')
                ->with('error', 'Error al restaurar el análisis: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}
