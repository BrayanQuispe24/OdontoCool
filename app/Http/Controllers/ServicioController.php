<?php

namespace App\Http\Controllers;

use App\Http\Requests\Servicio\StoreServicioRequest;
use App\Http\Requests\Servicio\UpdateServicioRequest;
use App\Http\Requests\Servicio\AsignarPrecioRequest;
use App\Models\Servicio;
use App\Models\Precio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ServicioController extends Controller
{
    public function index()
    {
        try {
            $servicios = Servicio::with(['asignacionesPrecio' => function($q) {
                $q->where('estado', 'ACTIVO');
            }, 'asignacionesPrecio.precio'])->get();

            return Inertia::render('Servicio/Index', [
                'servicios' => $servicios,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Servicio/Index', [
                'servicios' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function store(StoreServicioRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $servicio = Servicio::create([
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'] ?? null,
                'tipo' => $data['tipo'],
                'estado' => $data['estado'] ?? 'ACTIVO',
            ]);

            $precio = Precio::firstOrCreate([
                'monto' => $data['monto'],
                'moneda' => $data['moneda'],
            ], [
                'estado' => 'activo'
            ]);

            $servicio->asignacionesPrecio()->create([
                'precio_id' => $precio->id,
                'fecha_inicio' => $data['fecha_inicio'],
                'fecha_fin' => $data['fecha_fin'],
                'estado' => 'ACTIVO',
            ]);

            DB::commit();

            return redirect()->route('servicio.index')
                ->with('success', 'Servicio creado exitosamente')
                ->with('flash_id', uniqid());

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('servicio.index')
                ->with('error', 'Error al crear el servicio: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function show(int $servicio_id)
    {
        try {
            $servicio = Servicio::select([
                'id',
                'nombre',
                'descripcion',
                'tipo',
                'estado',
            ])
                ->with([
                    'asignacionesPrecio',
                    'asignacionesPrecio.precio',
                ])
                ->findOrFail($servicio_id);

            $precios = Precio::all();

            return Inertia::render('Servicio/Show', [
                'servicio' => $servicio,
                'precios' => $precios,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Servicio/Show', [
                'servicio' => null,
                'precios' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function update(UpdateServicioRequest $request, int $servicio_id)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $servicio = Servicio::findOrFail($servicio_id);
            
            $servicio->update([
                'nombre' => $data['nombre'] ?? $servicio->nombre,
                'descripcion' => array_key_exists('descripcion', $data) ? $data['descripcion'] : $servicio->descripcion,
                'tipo' => $data['tipo'] ?? $servicio->tipo,
                'estado' => $data['estado'] ?? $servicio->estado,
            ]);

            if (isset($data['monto']) && isset($data['moneda'])) {
                $activeAsig = $servicio->asignacionesPrecio()
                    ->where('estado', 'ACTIVO')
                    ->with('precio')
                    ->first();

                $hasPriceChanged = !$activeAsig ||
                    $activeAsig->precio->monto != $data['monto'] ||
                    $activeAsig->precio->moneda != $data['moneda'] ||
                    $activeAsig->fecha_inicio != $data['fecha_inicio'] ||
                    $activeAsig->fecha_fin != $data['fecha_fin'];

                if ($hasPriceChanged) {
                    $servicio->asignacionesPrecio()->where('estado', 'ACTIVO')->update([
                        'estado' => 'INACTIVO'
                    ]);

                    $precio = Precio::firstOrCreate([
                        'monto' => $data['monto'],
                        'moneda' => $data['moneda'],
                    ], [
                        'estado' => 'activo'
                    ]);

                    $servicio->asignacionesPrecio()->create([
                        'precio_id' => $precio->id,
                        'fecha_inicio' => $data['fecha_inicio'],
                        'fecha_fin' => $data['fecha_fin'],
                        'estado' => 'ACTIVO',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('servicio.index')
                ->with('success', 'Servicio actualizado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('servicio.index')
                ->with('error', 'Error al actualizar el servicio: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function destroy(int $servicio_id)
    {
        DB::beginTransaction();
        try {
            $servicio = Servicio::findOrFail($servicio_id);
            $servicio->update(['estado' => 'INACTIVO']);
            DB::commit();

            return redirect()->route('servicio.index')
                ->with('success', 'Servicio inactivado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('servicio.index')
                ->with('error', 'Error al inactivar el servicio: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function restore(int $servicio_id)
    {
        DB::beginTransaction();
        try {
            $servicio = Servicio::findOrFail($servicio_id);
            $servicio->update(['estado' => 'ACTIVO']);
            DB::commit();

            return redirect()->route('servicio.index')
                ->with('success', 'Servicio restaurado exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('servicio.index')
                ->with('error', 'Error al restaurar el servicio: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function asignarPrecio(AsignarPrecioRequest $request, int $servicio_id)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $servicio = Servicio::findOrFail($servicio_id);
            
            $servicio->asignacionesPrecio()->create([
                'precio_id' => $data['precio_id'],
                'fecha_inicio' => $data['fecha_inicio'],
                'fecha_fin' => $data['fecha_fin'],
                'estado' => $data['estado'] ?? 'ACTIVO',
            ]);

            DB::commit();

            return redirect()->route('servicio.show', ['servicio_id' => $servicio_id])
                ->with('success', 'Precio asignado exitosamente al servicio')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('servicio.show', ['servicio_id' => $servicio_id])
                ->with('error', 'Error al asignar el precio: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    public function eliminarPrecio(int $servicio_id, int $asignacion_id)
    {
        DB::beginTransaction();
        try {
            $servicio = Servicio::findOrFail($servicio_id);
            
            $asignacion = $servicio->asignacionesPrecio()->findOrFail($asignacion_id);
            $asignacion->delete();

            DB::commit();

            return redirect()->route('servicio.show', ['servicio_id' => $servicio_id])
                ->with('success', 'Asignación de precio eliminada exitosamente')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('servicio.show', ['servicio_id' => $servicio_id])
                ->with('error', 'Error al eliminar la asignación de precio: '.$e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}

