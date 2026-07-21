<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Bitacora\StoreBitacoraRequest;
use App\Models\Bitacora;
use Inertia\Inertia;

class BitacoraController extends Controller
{
    public function index()
    {
        try {
            $bitacoras = Bitacora::with('persona')
                ->orderBy('fecha', 'desc')
                ->orderBy('hora', 'desc')
                ->get();

            return Inertia::render('Bitacora/Index', [
                'bitacoras' => $bitacoras,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Bitacora/Index', [
                'bitacoras' => [],
                'error' => 'Error al obtener la bitácora: ' . $e->getMessage(),
            ]);
        }
    }

    public function store(StoreBitacoraRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Automatically capture IP and User Agent if not provided
            if (empty($data['ip_address'])) {
                $data['ip_address'] = $request->ip();
            }
            if (empty($data['user_agent'])) {
                $data['user_agent'] = $request->userAgent();
            }

            Bitacora::create($data);

            return redirect()
                ->back()
                ->with('success', 'Registro de bitácora creado correctamente');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al guardar en bitácora: ' . $e->getMessage());
        }
    }

    public function show(int $id)
    {
        try {
            $bitacora = Bitacora::with('persona')->findOrFail($id);

            return Inertia::render('Bitacora/Show', [
                'bitacora' => $bitacora,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('bitacora.index')
                ->with('error', 'Error al cargar el registro de bitácora: ' . $e->getMessage());
        }
    }
}
