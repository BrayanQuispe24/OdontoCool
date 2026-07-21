<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tratamiento\StoreTratamientoRequest;
use App\Http\Requests\Tratamiento\UpdateTratamientoRequest;
use App\Models\Tratamiento;
use App\Models\Diente;
use App\Models\Diagnostico;
use App\Models\Servicio;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TratamientoController extends Controller
{
    public function index()
    {
        try {
            $tratamientos = Tratamiento::with([
                'tratamientosDientes.diente',
                'recetaRecomendaciones.detalles',
                'diagnostico.cita.paciente.persona',
                'historialClinico.paciente.persona',
                'servicioPrestado' => function ($q) {
                    $q->where('estado', '!=', 'INACTIVO');
                },
                'servicioPrestado.servicio'
            ])->get();
            
            $dientes = Diente::activo()->get();

            // Get diagnoses that do not have a treatment yet
            $diagnosticosDisponibles = Diagnostico::with('cita.paciente.persona')
                ->whereNotIn('id', function ($query) {
                    $query->select('diagnostico_id')->from('tratamientos')->whereNotNull('diagnostico_id');
                })
                ->get();

            $diagnosticosTodos = Diagnostico::with('cita.paciente.persona')->get();
            $historiales = \App\Models\HistorialClinico::activo()->with('paciente.persona')->get();

            $servicios = Servicio::activo()
                ->with(['asignacionesPrecio' => function ($q) {
                    $q->where('estado', 'ACTIVO');
                }, 'asignacionesPrecio.precio'])
                ->get();

            return Inertia::render('Tratamiento/Index', [
                'tratamientos' => $tratamientos,
                'dientes' => $dientes,
                'diagnosticosDisponibles' => $diagnosticosDisponibles,
                'diagnosticosTodos' => $diagnosticosTodos,
                'historiales' => $historiales,
                'servicios' => $servicios,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Tratamiento/Index', [
                'tratamientos' => [],
                'dientes' => [],
                'diagnosticosDisponibles' => [],
                'diagnosticosTodos' => [],
                'historiales' => [],
                'servicios' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function store(StoreTratamientoRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $tratamiento = Tratamiento::create([
                'objetivo_tratamiento' => $data['objetivo_tratamiento'],
                'observacion' => $data['observacion'] ?? null,
                'estado' => $data['estado'] ?? 'ACTIVO',
                'fecha_inicio' => $data['fecha_inicio'],
                'fecha_fin_estimada' => $data['fecha_fin_estimada'],
                'fecha_fin_real' => $data['fecha_fin_real'] ?? null,
                'diagnostico_id' => $data['diagnostico_id'],
                'codigo_historial' => $data['codigo_historial'],
            ]);

            if (!empty($data['dientes_tratamientos'])) {
                foreach ($data['dientes_tratamientos'] as $dt) {
                    $tratamiento->tratamientosDientes()->create([
                        'cara_dental' => $dt['cara_dental'],
                        'observacion' => $dt['observacion'] ?? null,
                        'fecha_registro' => now()->toDateString(),
                        'tratamiento_planificado' => $dt['tratamiento_planificado'],
                        'estado' => 'ACTIVO',
                        'diente_id' => $dt['diente_id'],
                    ]);
                }
            }

            // Create recipe if sent
            if (isset($data['receta'])) {
                $receta = $tratamiento->recetaRecomendaciones()->create([
                    'fecha_emision' => now()->toDateString(),
                    'observacion_general' => $data['receta']['observacion_general'] ?? null,
                ]);

                if (isset($data['receta']['detalles']) && is_array($data['receta']['detalles'])) {
                    foreach ($data['receta']['detalles'] as $det) {
                        $receta->detalles()->create([
                            'descripcion' => $det['descripcion'],
                            'dosis' => $det['dosis'],
                            'duracion' => $det['duracion'],
                            'frecuencia' => $det['frecuencia'],
                        ]);
                    }
                }
            }

            // Create services rendered if sent
            if (!empty($data['servicios_prestados'])) {
                foreach ($data['servicios_prestados'] as $sp) {
                    $precio = $sp['precio'];
                    $cantidad = $sp['cantidad'];
                    $descuento = $sp['descuento'] ?? 0;
                    $subtotal = ($precio * $cantidad) - $descuento;

                    $tratamiento->servicioPrestado()->create([
                        'cantidad' => $cantidad,
                        'precio' => $precio,
                        'descuento' => $descuento,
                        'subtotal' => $subtotal,
                        'fecha_servicio' => $sp['fecha_servicio'],
                        'observacion' => $sp['observacion'] ?? null,
                        'servicio_id' => $sp['servicio_id'],
                        'estado' => 'ACTIVO',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('tratamiento.index')
                ->with('success', 'Tratamiento creado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('tratamiento.index')
                ->with('error', 'Error al crear el tratamiento: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function show(int $tratamiento_id)
    {
        try {
            $tratamiento = Tratamiento::with([
                'tratamientosDientes.diente',
                'recetaRecomendaciones.detalles',
                'solicitudAnalisis.resultadoAnalisis',
                'solicitudAnalisis.analisis',
                'diagnostico.cita.paciente.persona',
                'historialClinico.paciente.persona',
                'servicioPrestado' => function($q) {
                    $q->where('estado', 'ACTIVO')->with('servicio');
                }
            ])->findOrFail($tratamiento_id);

            return Inertia::render('Tratamiento/Show', [
                'tratamiento' => $tratamiento,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Tratamiento/Show', [
                'tratamiento' => null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function update(UpdateTratamientoRequest $request, int $tratamiento_id)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $tratamiento = Tratamiento::findOrFail($tratamiento_id);
            
            $diagnosticoId = $data['diagnostico_id'] ?? $tratamiento->diagnostico_id;
            $codigo_historial = $data['codigo_historial'] ?? $tratamiento->codigo_historial;

            $tratamiento->update([
                'objetivo_tratamiento' => $data['objetivo_tratamiento'] ?? $tratamiento->objetivo_tratamiento,
                'observacion' => array_key_exists('observacion', $data) ? $data['observacion'] : $tratamiento->observacion,
                'estado' => $data['estado'] ?? $tratamiento->estado,
                'fecha_inicio' => $data['fecha_inicio'] ?? $tratamiento->fecha_inicio,
                'fecha_fin_estimada' => $data['fecha_fin_estimada'] ?? $tratamiento->fecha_fin_estimada,
                'fecha_fin_real' => array_key_exists('fecha_fin_real', $data) ? $data['fecha_fin_real'] : $tratamiento->fecha_fin_real,
                'diagnostico_id' => $diagnosticoId,
                'codigo_historial' => $codigo_historial,
            ]);

            if (isset($data['dientes_tratamientos'])) {
                $updatedIds = collect($data['dientes_tratamientos'])->pluck('id')->filter()->toArray();
                $tratamiento->tratamientosDientes()->whereNotIn('id', $updatedIds)->delete();

                foreach ($data['dientes_tratamientos'] as $dt) {
                    if (isset($dt['id'])) {
                        $existing = $tratamiento->tratamientosDientes()->findOrFail($dt['id']);
                        $existing->update([
                            'cara_dental' => $dt['cara_dental'],
                            'observacion' => $dt['observacion'] ?? null,
                            'tratamiento_planificado' => $dt['tratamiento_planificado'],
                            'diente_id' => $dt['diente_id'],
                        ]);
                    } else {
                        $tratamiento->tratamientosDientes()->create([
                            'cara_dental' => $dt['cara_dental'],
                            'observacion' => $dt['observacion'] ?? null,
                            'fecha_registro' => now()->toDateString(),
                            'tratamiento_planificado' => $dt['tratamiento_planificado'],
                            'estado' => 'ACTIVO',
                            'diente_id' => $dt['diente_id'],
                        ]);
                    }
                }
            }

            // Update recipe if sent
            if (isset($data['receta'])) {
                $receta = $tratamiento->recetaRecomendaciones;
                if (!$receta) {
                    $receta = $tratamiento->recetaRecomendaciones()->create([
                        'fecha_emision' => now()->toDateString(),
                        'observacion_general' => $data['receta']['observacion_general'] ?? null,
                    ]);
                } else {
                    $receta->update([
                        'fecha_emision' => now()->toDateString(),
                        'observacion_general' => $data['receta']['observacion_general'] ?? null,
                    ]);
                }

                if (isset($data['receta']['detalles'])) {
                        $updatedDetailIds = collect($data['receta']['detalles'])->pluck('id')->filter()->toArray();
                        $receta->detalles()->whereNotIn('id', $updatedDetailIds)->delete();

                        foreach ($data['receta']['detalles'] as $det) {
                            if (isset($det['id'])) {
                                $existingDetail = $receta->detalles()->findOrFail($det['id']);
                                $existingDetail->update([
                                    'descripcion' => $det['descripcion'],
                                    'dosis' => $det['dosis'],
                                    'duracion' => $det['duracion'],
                                    'frecuencia' => $det['frecuencia'],
                                ]);
                            } else {
                                $receta->detalles()->create([
                                    'descripcion' => $det['descripcion'],
                                    'dosis' => $det['dosis'],
                                    'duracion' => $det['duracion'],
                                    'frecuencia' => $det['frecuencia'],
                                ]);
                            }
                        }
                    }
            }

            if (isset($data['servicios_prestados'])) {
                $updatedSpIds = collect($data['servicios_prestados'])->pluck('id')->filter()->toArray();
                // Inactivar (lógica de eliminación lógica) los que fueron removidos
                $tratamiento->servicioPrestado()->whereNotIn('id', $updatedSpIds)->update(['estado' => 'INACTIVO']);

                foreach ($data['servicios_prestados'] as $sp) {
                    $precio = $sp['precio'];
                    $cantidad = $sp['cantidad'];
                    $descuento = $sp['descuento'] ?? 0;
                    $subtotal = ($precio * $cantidad) - $descuento;

                    if (isset($sp['id'])) {
                        $existingSp = $tratamiento->servicioPrestado()->findOrFail($sp['id']);
                        $existingSp->update([
                            'cantidad' => $cantidad,
                            'precio' => $precio,
                            'descuento' => $descuento,
                            'subtotal' => $subtotal,
                            'fecha_servicio' => $sp['fecha_servicio'],
                            'observacion' => $sp['observacion'] ?? null,
                            'servicio_id' => $sp['servicio_id'],
                            'estado' => 'ACTIVO',
                        ]);
                    } else {
                        $tratamiento->servicioPrestado()->create([
                            'cantidad' => $cantidad,
                            'precio' => $precio,
                            'descuento' => $descuento,
                            'subtotal' => $subtotal,
                            'fecha_servicio' => $sp['fecha_servicio'],
                            'observacion' => $sp['observacion'] ?? null,
                            'servicio_id' => $sp['servicio_id'],
                            'estado' => 'ACTIVO',
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('tratamiento.index')
                ->with('success', 'Tratamiento actualizado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('tratamiento.index')
                ->with('error', 'Error al actualizar el tratamiento: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function destroy(int $tratamiento_id)
    {
        DB::beginTransaction();
        try {
            $tratamiento = Tratamiento::findOrFail($tratamiento_id);
            $tratamiento->update(['estado' => 'INACTIVO']);
            DB::commit();

            return redirect()->route('tratamiento.index')
                ->with('success', 'Tratamiento inactivado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('tratamiento.index')
                ->with('error', 'Error al inactivar el tratamiento: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function restore(int $tratamiento_id)
    {
        DB::beginTransaction();
        try {
            $tratamiento = Tratamiento::findOrFail($tratamiento_id);
            $tratamiento->update(['estado' => 'ACTIVO']);
            DB::commit();

            return redirect()->route('tratamiento.index')
                ->with('success', 'Tratamiento restaurado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('tratamiento.index')
                ->with('error', 'Error al restaurar el tratamiento: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}
