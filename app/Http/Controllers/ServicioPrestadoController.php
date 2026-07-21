<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServicioPrestado\StoreServicioPrestadoRequest;
use App\Http\Requests\ServicioPrestado\UpdateServicioPrestadoRequest;
use App\Models\ServicioPrestado;
use App\Models\Servicio;
use App\Models\Tratamiento;
use App\Models\BoletaPago;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ServicioPrestadoController extends Controller
{
    /**
     * Display a listing of the rendered services.
     */
    public function index(): Response
    {
        try {
            // Get active rendered services along with relationships
            $serviciosPrestados = ServicioPrestado::activo()
                ->with([
                    'servicio',
                    'tratamiento.historialClinico.paciente.persona',
                    'boletaPago'
                ])
                ->latest()
                ->get();

            // Fetch active services and active treatments for forms
            $servicios = Servicio::activo()
                ->with(['asignacionesPrecio' => function ($q) {
                    $q->where('estado', 'ACTIVO');
                }, 'asignacionesPrecio.precio'])
                ->get();

            $tratamientos = Tratamiento::activo()
                ->with('historialClinico.paciente.persona')
                ->get();

            $boletas = BoletaPago::where('estado_pago', '!=', 'ELIMINADO')->latest()->get();

            return Inertia::render('ServicioPrestado/Index', [
                'serviciosPrestados' => $serviciosPrestados,
                'servicios' => $servicios,
                'tratamientos' => $tratamientos,
                'boletas' => $boletas,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('ServicioPrestado/Index', [
                'serviciosPrestados' => [],
                'servicios' => [],
                'tratamientos' => [],
                'boletas' => [],
                'error' => 'Error al cargar los servicios prestados: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created rendered service in storage.
     */
    public function store(StoreServicioPrestadoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Calculate subtotal: (precio * cantidad) - descuento
        $precio = $data['precio'];
        $cantidad = $data['cantidad'];
        $descuento = $data['descuento'] ?? 0;
        $subtotal = ($precio * $cantidad) - $descuento;
        
        DB::beginTransaction();
        try {
            ServicioPrestado::create([
                'cantidad' => $cantidad,
                'precio' => $precio,
                'descuento' => $descuento,
                'subtotal' => $subtotal,
                'fecha_servicio' => $data['fecha_servicio'],
                'observacion' => $data['observacion'] ?? null,
                'tratamiento_id' => $data['tratamiento_id'],
                'servicio_id' => $data['servicio_id'],
                'id_boleta' => $data['id_boleta'] ?? null,
                'estado' => $data['estado'] ?? 'ACTIVO',
            ]);

            DB::commit();

            return redirect()->route('servicio_prestado.index')
                ->with('success', 'Servicio prestado registrado exitosamente.')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('servicio_prestado.index')
                ->with('error', 'Error al registrar el servicio prestado: ' . $e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    /**
     * Display the specified rendered service.
     */
    public function show(int $id): Response|RedirectResponse
    {
        try {
            $servicioPrestado = ServicioPrestado::activo()
                ->with([
                    'servicio',
                    'tratamiento.historialClinico.paciente.persona',
                    'boletaPago'
                ])
                ->findOrFail($id);

            return Inertia::render('ServicioPrestado/Show', [
                'servicioPrestado' => $servicioPrestado
            ]);
        } catch (\Exception $e) {
            return redirect()->route('servicio_prestado.index')
                ->with('error', 'Servicio prestado no encontrado: ' . $e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    /**
     * Update the specified rendered service in storage.
     */
    public function update(UpdateServicioPrestadoRequest $request, int $id): RedirectResponse
    {
        $data = $request->validated();
        
        DB::beginTransaction();
        try {
            $servicioPrestado = ServicioPrestado::findOrFail($id);

            // Re-calculate subtotal if cantidad, precio, or descuento are being updated
            $cantidad = $data['cantidad'] ?? $servicioPrestado->cantidad;
            $precio = $data['precio'] ?? $servicioPrestado->precio;
            $descuento = array_key_exists('descuento', $data) ? ($data['descuento'] ?? 0) : $servicioPrestado->descuento;
            $subtotal = ($precio * $cantidad) - $descuento;

            $servicioPrestado->update([
                'cantidad' => $cantidad,
                'precio' => $precio,
                'descuento' => $descuento,
                'subtotal' => $subtotal,
                'fecha_servicio' => $data['fecha_servicio'] ?? $servicioPrestado->fecha_servicio,
                'observacion' => array_key_exists('observacion', $data) ? $data['observacion'] : $servicioPrestado->observacion,
                'tratamiento_id' => $data['tratamiento_id'] ?? $servicioPrestado->tratamiento_id,
                'servicio_id' => $data['servicio_id'] ?? $servicioPrestado->servicio_id,
                'id_boleta' => array_key_exists('id_boleta', $data) ? $data['id_boleta'] : $servicioPrestado->id_boleta,
                'estado' => $data['estado'] ?? $servicioPrestado->estado,
            ]);

            DB::commit();

            return redirect()->route('servicio_prestado.index')
                ->with('success', 'Servicio prestado actualizado exitosamente.')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('servicio_prestado.index')
                ->with('error', 'Error al actualizar el servicio prestado: ' . $e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    /**
     * Remove the specified rendered service from storage logically (Soft Delete).
     */
    public function destroy(int $id): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $servicioPrestado = ServicioPrestado::findOrFail($id);
            
            // Logical Delete
            $servicioPrestado->update(['estado' => 'INACTIVO']);
            
            DB::commit();

            return redirect()->route('servicio_prestado.index')
                ->with('success', 'Servicio prestado eliminado lógicamente exitosamente.')
                ->with('flash_id', uniqid());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('servicio_prestado.index')
                ->with('error', 'Error al eliminar el servicio prestado: ' . $e->getMessage())
                ->with('flash_id', uniqid());
        }
    }
}
