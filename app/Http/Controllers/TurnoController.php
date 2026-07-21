<?php

namespace App\Http\Controllers;

use App\Http\Requests\Turno\StoreTurnoRequest;
use App\Http\Requests\Turno\UpdateTurnoRequest;
use App\Models\Turno;
use Inertia\Inertia;

class TurnoController extends Controller
{
    public function index()
    {
        try {
            $turnos = Turno::all();

            return Inertia::render('Turno/Index', [
                'turnos' => $turnos,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Turno/Index', [
                'turnos' => [],
                'error' => 'Error al obtener los turnos: '.$e->getMessage(),
            ]);
        }
    }

    public function store(StoreTurnoRequest $request)
    {
        try {
            $data = $request->validated();
            Turno::create($data);

            return redirect()
                ->back()
                ->with('success', 'Turno creado correctamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al crear el turno: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function show(int $turno_id)
    {
        try {
            $turno = Turno::findOrFail($turno_id);

            // return Inertia::render('Turno/Show', [
            //     'turno' => $turno,
            // ]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al obtener el turno: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function update(UpdateTurnoRequest $request, int $turno_id)
    {
        try {
            $turno = Turno::findOrFail($turno_id);
            $data = $request->validated();
            $turno->update($data);

            return redirect()
                ->back()
                ->with('success', 'Turno actualizado correctamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al actualizar el turno: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function destroy(int $turno_id)
    {
        try {
            $turno = Turno::findOrFail($turno_id);
            $turno->delete();

            return redirect()
                ->back()
                ->with('success', 'Turno eliminado correctamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al eliminar el turno: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}
