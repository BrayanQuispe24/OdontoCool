<?php

namespace App\Http\Controllers;

use App\Http\Requests\SolicitudAnalisis\StoreSolicitudAnalisisRequest;
use App\Http\Requests\SolicitudAnalisis\UpdateSolicitudAnalisisRequest;
use App\Models\SolicitudAnalisis;
use App\Models\Analisis;
use App\Models\Tratamiento;
use App\Models\Doctor;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SolicitudAnalisisController extends Controller
{
    public function index()
    {
        try {
            $solicitudes = SolicitudAnalisis::activo()->with([
                'analisis', 
                'tratamiento', 
                'doctor.persona',
                'resultadoAnalisis' => function($query) {
                    $query->activo();
                }
            ])->get();
            $analisis = Analisis::activo()->get();
            $tratamientos = Tratamiento::activo()->get();
            $doctores = Doctor::with('persona')->get();

            return Inertia::render('SolicitudAnalisis/Index', [
                'solicitudes' => $solicitudes,
                'analisis' => $analisis,
                'tratamientos' => $tratamientos,
                'doctores' => $doctores,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('SolicitudAnalisis/Index', [
                'solicitudes' => [],
                'analisis' => [],
                'tratamientos' => [],
                'doctores' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function store(StoreSolicitudAnalisisRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            SolicitudAnalisis::create($data);
            DB::commit();

            return redirect()->route('analisis.index')
                ->with('success', 'Solicitud de análisis creada exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('analisis.index')
                ->with('error', 'Error al crear la solicitud: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function show(int $id)
    {
        try {
            $solicitud = SolicitudAnalisis::with(['analisis', 'tratamiento', 'resultadoAnalisis'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $solicitud,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateSolicitudAnalisisRequest $request, int $id)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $solicitud = SolicitudAnalisis::findOrFail($id);
            $solicitud->update($data);
            DB::commit();

            return redirect()->route('analisis.index')
                ->with('success', 'Solicitud de análisis actualizada exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('analisis.index')
                ->with('error', 'Error al actualizar la solicitud: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();
        try {
            $solicitud = SolicitudAnalisis::findOrFail($id);
            $solicitud->update(['estado' => 'INACTIVO']);
            DB::commit();

            return redirect()->route('analisis.index')
                ->with('success', 'Solicitud de análisis inactivada exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('analisis.index')
                ->with('error', 'Error al inactivar la solicitud: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}
