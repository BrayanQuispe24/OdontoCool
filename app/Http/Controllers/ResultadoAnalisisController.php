<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResultadoAnalisis\StoreResultadoAnalisisRequest;
use App\Http\Requests\ResultadoAnalisis\UpdateResultadoAnalisisRequest;
use App\Models\ResultadoAnalisis;
use App\Models\SolicitudAnalisis;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ResultadoAnalisisController extends Controller
{
    public function index()
    {
        try {
            $resultados = ResultadoAnalisis::activo()->with(['solicitudAnalisis.analisis', 'solicitudAnalisis.tratamiento'])->get();
            $solicitudes = SolicitudAnalisis::activo()->get();

            return Inertia::render('ResultadoAnalisis/Index', [
                'resultados' => $resultados,
                'solicitudes' => $solicitudes,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('ResultadoAnalisis/Index', [
                'resultados' => [],
                'solicitudes' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function store(StoreResultadoAnalisisRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            if ($request->hasFile('archivo_resultado')) {
                $file = $request->file('archivo_resultado');
                $solicitudId = $data['solicitud_analisis_id'];
                $filename = 'solicitud_' . $solicitudId . '_' . now()->format('Ymd_His') . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('resultados', $filename, 'public');
                $data['archivo_resultado'] = $path;
            }

            ResultadoAnalisis::create($data);

            // Actualizar el estado de la solicitud asociada a COMPLETADO
            $solicitud = SolicitudAnalisis::findOrFail($data['solicitud_analisis_id']);
            $solicitud->update(['estado' => 'COMPLETADO']);

            DB::commit();

            return redirect()->route('analisis.index')
                ->with('success', 'Resultado de análisis registrado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('analisis.index')
                ->with('error', 'Error al registrar el resultado: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function show(int $id)
    {
        try {
            $resultado = ResultadoAnalisis::with(['solicitudAnalisis.analisis', 'solicitudAnalisis.tratamiento'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $resultado,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateResultadoAnalisisRequest $request, int $id)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $resultado = ResultadoAnalisis::findOrFail($id);

            if ($request->hasFile('archivo_resultado')) {
                // Borrar archivo anterior de storage si existe
                if ($resultado->archivo_resultado && \Illuminate\Support\Facades\Storage::disk('public')->exists($resultado->archivo_resultado)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($resultado->archivo_resultado);
                }

                $file = $request->file('archivo_resultado');
                $solicitudId = $request->input('solicitud_analisis_id', $resultado->solicitud_analisis_id);
                $filename = 'solicitud_' . $solicitudId . '_' . now()->format('Ymd_His') . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('resultados', $filename, 'public');
                $data['archivo_resultado'] = $path;
            }

            $resultado->update($data);
            DB::commit();

            return redirect()->route('analisis.index')
                ->with('success', 'Resultado de análisis actualizado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('analisis.index')
                ->with('error', 'Error al actualizar el resultado: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();
        try {
            $resultado = ResultadoAnalisis::findOrFail($id);
            $resultado->update(['estado' => 'INACTIVO']);
            DB::commit();

            return redirect()->route('analisis.index')
                ->with('success', 'Resultado de análisis inactivado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('analisis.index')
                ->with('error', 'Error al inactivar el resultado: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}
