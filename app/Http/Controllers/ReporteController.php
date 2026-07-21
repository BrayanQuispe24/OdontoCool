<?php

namespace App\Http\Controllers;

use App\Models\BoletaPago;
use App\Models\Cita;
use App\Models\Doctor;
use App\Models\ServicioPrestado;
use App\Models\SolicitudAnalisis;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio')
            ? Carbon::parse($request->input('fecha_inicio'))->toDateString()
            : now()->startOfMonth()->toDateString();

        $fechaFin = $request->input('fecha_fin')
            ? Carbon::parse($request->input('fecha_fin'))->toDateString()
            : now()->endOfMonth()->toDateString();

        $doctorCi = $request->input('doctor_ci');

        /*
        |--------------------------------------------------------------------------
        | Doctores para el filtro
        |--------------------------------------------------------------------------
        */

        $doctores = Doctor::query()
            ->with('persona:carnet_identidad,nombre,apellido')
            ->orderBy('codigo_doctor')
            ->get()
            ->map(function ($doctor) {
                $nombreCompleto = trim(
                    ($doctor->persona->nombre ?? '') . ' ' . ($doctor->persona->apellido ?? '')
                );

                return [
                    'codigo_doctor' => $doctor->codigo_doctor,
                    'nombre_completo' => $nombreCompleto !== ''
                        ? $nombreCompleto
                        : $doctor->codigo_doctor,
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Citas realizadas por fecha
        |--------------------------------------------------------------------------
        */

        $citasPorFecha = Cita::query()
            ->when($doctorCi, function ($query) use ($doctorCi) {
                $query->where('doctor_ci', $doctorCi);
            })
            ->whereBetween('fecha_cita', [$fechaInicio, $fechaFin])
            ->selectRaw("
                TO_CHAR(fecha_cita, 'YYYY-MM-DD') as fecha,
                COUNT(*) as cantidad
            ")
            ->groupByRaw("TO_CHAR(fecha_cita, 'YYYY-MM-DD')")
            ->orderBy('fecha')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Citas por doctor
        |--------------------------------------------------------------------------
        */

        $citasPorDoctor = Cita::query()
            ->leftJoin('doctores', 'citas.doctor_ci', '=', 'doctores.codigo_doctor')
            ->leftJoin('personas', 'doctores.persona_id', '=', 'personas.carnet_identidad')
            ->whereBetween('citas.fecha_cita', [$fechaInicio, $fechaFin])
            ->selectRaw("
                COALESCE(CONCAT(personas.nombre, ' ', personas.apellido), 'Sin doctor') as doctor,
                COUNT(*) as cantidad
            ")
            ->groupBy('doctores.codigo_doctor', 'personas.nombre', 'personas.apellido')
            ->orderByDesc('cantidad')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Servicios más prestados
        |--------------------------------------------------------------------------
        */

        $serviciosMasPrestados = ServicioPrestado::query()
            ->join('servicios', 'servicio_prestados.servicio_id', '=', 'servicios.id')
            ->whereBetween('servicio_prestados.fecha_servicio', [$fechaInicio, $fechaFin])
            ->where('servicio_prestados.estado', 'ACTIVO')
            ->selectRaw("
                servicios.nombre as servicio,
                SUM(servicio_prestados.cantidad) as cantidad,
                SUM(servicio_prestados.subtotal) as total
            ")
            ->groupBy('servicios.id', 'servicios.nombre')
            ->orderByDesc('cantidad')
            ->limit(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Usuarios registrados por fecha
        |--------------------------------------------------------------------------
        */

        $usuariosRegistrados = User::query()
            ->whereBetween('created_at', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay(),
            ])
            ->selectRaw("
                TO_CHAR(created_at, 'YYYY-MM-DD') as fecha,
                COUNT(*) as cantidad
            ")
            ->groupByRaw("TO_CHAR(created_at, 'YYYY-MM-DD')")
            ->orderBy('fecha')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Finanzas por fecha
        |--------------------------------------------------------------------------
        */

        $finanzasPorFecha = BoletaPago::query()
            ->whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
            ->selectRaw("
                TO_CHAR(fecha_emision, 'YYYY-MM-DD') as fecha,
                SUM(total) as total,
                COUNT(*) as cantidad
            ")
            ->groupByRaw("TO_CHAR(fecha_emision, 'YYYY-MM-DD')")
            ->orderBy('fecha')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Finanzas por estado de pago
        |--------------------------------------------------------------------------
        */

        $finanzasPorEstado = BoletaPago::query()
            ->whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
            ->selectRaw("
                estado_pago,
                COUNT(*) as cantidad,
                SUM(total) as total
            ")
            ->groupBy('estado_pago')
            ->orderByDesc('total')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Totales financieros
        |--------------------------------------------------------------------------
        */

        $totalIngresos = BoletaPago::query()
            ->whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
            ->sum('total');

        $totalPagado = BoletaPago::query()
            ->whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
            ->where('estado_pago', 'PAGADO')
            ->sum('total');

        $totalPendiente = BoletaPago::query()
            ->whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
            ->where('estado_pago', 'PENDIENTE')
            ->sum('total');

        /*
        |--------------------------------------------------------------------------
        | Solicitudes de análisis por tipo
        |--------------------------------------------------------------------------
        */

        $solicitudesAnalisis = SolicitudAnalisis::query()
            ->leftJoin('analisis', 'solicitud_analisis.analisis_id', '=', 'analisis.id')
            ->whereBetween('solicitud_analisis.fecha_solicitud', [$fechaInicio, $fechaFin])
            ->where('solicitud_analisis.estado', '!=', 'INACTIVO')
            ->selectRaw("
                COALESCE(analisis.nombre, 'Sin análisis') as analisis,
                COUNT(*) as cantidad
            ")
            ->groupBy('analisis.nombre')
            ->orderByDesc('cantidad')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Solicitudes de análisis por doctor
        |--------------------------------------------------------------------------
        */

        $solicitudesAnalisisPorDoctor = SolicitudAnalisis::query()
            ->leftJoin('doctores', 'solicitud_analisis.doctor_ci', '=', 'doctores.codigo_doctor')
            ->leftJoin('personas', 'doctores.persona_id', '=', 'personas.carnet_identidad')
            ->whereBetween('solicitud_analisis.fecha_solicitud', [$fechaInicio, $fechaFin])
            ->where('solicitud_analisis.estado', '!=', 'INACTIVO')
            ->selectRaw("
                COALESCE(CONCAT(personas.nombre, ' ', personas.apellido), 'Sin doctor') as doctor,
                COUNT(*) as cantidad
            ")
            ->groupBy('doctores.codigo_doctor', 'personas.nombre', 'personas.apellido')
            ->orderByDesc('cantidad')
            ->get();

        return Inertia::render('Reportes/Index', [
            'filtros' => [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'doctor_ci' => $doctorCi,
            ],

            'doctores' => $doctores,

            'resumen' => [
                'total_citas' => (int) $citasPorFecha->sum('cantidad'),
                'total_servicios' => (int) $serviciosMasPrestados->sum('cantidad'),
                'total_usuarios' => (int) $usuariosRegistrados->sum('cantidad'),
                'total_ingresos' => round((float) $totalIngresos, 2),
                'total_pagado' => round((float) $totalPagado, 2),
                'total_pendiente' => round((float) $totalPendiente, 2),
                'total_solicitudes_analisis' => (int) $solicitudesAnalisis->sum('cantidad'),
            ],

            'graficos' => [
                'citas_por_fecha' => $citasPorFecha,
                'citas_por_doctor' => $citasPorDoctor,
                'servicios_mas_prestados' => $serviciosMasPrestados,
                'usuarios_registrados' => $usuariosRegistrados,
                'finanzas_por_fecha' => $finanzasPorFecha,
                'finanzas_por_estado' => $finanzasPorEstado,
                'solicitudes_analisis' => $solicitudesAnalisis,
                'solicitudes_analisis_por_doctor' => $solicitudesAnalisisPorDoctor,
            ],
        ]);
    }
}