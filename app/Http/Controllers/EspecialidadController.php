<?php

namespace App\Http\Controllers;

use App\Http\Requests\Especialidades\StoreEspecialidadRequest;
use App\Http\Requests\Especialidades\UpdateEspecialidadRequest;
use App\Models\Especialidad;
use Inertia\Inertia;

class EspecialidadController extends Controller
{
    public function index()
    {
        try {
            $especialidades = Especialidad::all();

            return Inertia::render('Especialidad/Index', [
                'especialidades' => $especialidades,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Especialidad/Index', [
                'especialidades' => [],
                'error' => 'Error al obtener las especialidades: '.$e->getMessage(),
            ]);
        }
    }

    public function store(StoreEspecialidadRequest $request)
    {
        try {

            $data = $request->validated();
            $data['estado'] = 'activo'; // Establecer el estado por defecto como 'activo'
            $especialidad = Especialidad::create($data);

            return redirect()
                ->route('especialidad.index')
                ->with('success', 'Especialidad creada exitosamente.')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            return redirect()
                ->route('especialidad.index')
                ->with('error', 'Error al crear la especialidad: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function show(int $especialidad_id)
    {
        try {
            $especialidad = Especialidad::findOrFail($especialidad_id);

            return Inertia::render('Especialidad/Show', [
                'especialidad' => $especialidad,
            ]);
        } catch (\Exception $e) {
            return redirect()
                ->route('especialidad.index')
                ->with('error', 'Error al obtener la especialidad: '.$e->getMessage())
                ->with('flash_id', uniqid());
        } 
    }

    public function update(UpdateEspecialidadRequest $request, int $especialidad_id)
    {
        try {
            $especialidad = Especialidad::findOrFail($especialidad_id);
            $data = $request->validated();
            $especialidad->update($data);

            return redirect()
                ->route('especialidad.index')
                ->with('success', 'Especialidad actualizada exitosamente.')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            return redirect()
                ->route('especialidad.index')
                ->with('error', 'Error al actualizar la especialidad: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function destroy(int $especialidad_id)
    {
        try {
            $especialidad = Especialidad::findOrFail($especialidad_id);
            $especialidad->delete();

            return redirect()
                ->route('especialidad.index')
                ->with('success', 'Especialidad eliminada exitosamente.')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            return redirect()
                ->route('especialidad.index')
                ->with('error', 'Error al eliminar la especialidad: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}
