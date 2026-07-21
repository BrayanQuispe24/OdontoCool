<?php

namespace App\Http\Controllers;

use App\Http\Requests\BoletaPago\StoreBoletaPagoRequest;
use App\Http\Requests\BoletaPago\PayCuotaRequest;
use App\Models\BoletaPago;
use App\Models\ModoPago;
use App\Models\CuotaBoleta;
use App\Models\Paciente;
use App\Models\MetodoPago;
use App\Models\Secretaria;
use App\Models\ServicioPrestado;
use App\Models\Tratamiento;
use App\Services\PagoFacilService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;

class BoletaPagoController extends Controller
{
    /**
     * Display a listing of billing sheets and payment statuses.
     */
    public function index()
    {
        try {
            /** @var User|null $user */
            $user = Auth::user();
            $boletasQuery = BoletaPago::with([
                'modoPago',
                'paciente.persona',
                'secretaria.persona',
                'cuotaBoleta' => function($q) {
                    $q->with('metodoPago', 'cuotaMulta');
                },
                'servicioPrestado.servicio'
            ]);

            if($user->rol->nombre==='Paciente'){
                $paciente = $user->persona->pacientes;
                $boletasQuery->where('paciente_ci', $paciente->codigo_paciente);
            }

            $boletas = $boletasQuery->latest()->get()->values();

            $modoPagos = ModoPago::activo()->get();
            $metodoPagos = MetodoPago::get();
            $pacientes = Paciente::with('persona')->get();
            $secretarias = Secretaria::with('persona')->get();

            // Unbilled services (available for adding to a new bill)
            $serviciosDisponibles = ServicioPrestado::whereNull('id_boleta')
                ->where('estado', 'ACTIVO')
                ->with(['servicio', 'tratamiento.historialClinico.paciente.persona'])
                ->get();

            // Treatments with unbilled services
            $tratamientosDisponibles = Tratamiento::whereHas('servicioPrestado', function($q) {
                $q->whereNull('id_boleta')->where('estado', 'ACTIVO');
            })->with([
                'historialClinico.paciente.persona',
                'servicioPrestado' => function($q) {
                    $q->whereNull('id_boleta')->where('estado', 'ACTIVO')->with('servicio');
                }
            ])->get();

            return Inertia::render('BoletaPago/Index', [
                'boletas' => $boletas,
                'modoPagos' => $modoPagos,
                'metodoPagos' => $metodoPagos,
                'pacientes' => $pacientes,
                'secretarias' => $secretarias,
                'serviciosDisponibles' => $serviciosDisponibles,
                'tratamientosDisponibles' => $tratamientosDisponibles,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('BoletaPago/Index', [
                'boletas' => [],
                'modoPagos' => [],
                'metodoPagos' => [],
                'pacientes' => [],
                'secretarias' => [],
                'serviciosDisponibles' => [],
                'tratamientosDisponibles' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Store a newly created billing sheet and generate installments.
     */
    public function store(StoreBoletaPagoRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Fetch unbilled services to be billed
            $servicios = ServicioPrestado::whereIn('id', $data['servicio_prestado_ids'])
                ->whereNull('id_boleta')
                ->where('estado', 'ACTIVO')
                ->get();

            if ($servicios->isEmpty()) {
                throw new \Exception('No se seleccionaron servicios válidos o ya han sido facturados.');
            }

            // Calculations
            $subtotal = $servicios->sum('subtotal');
            $descuento = $data['descuento'] ?? 0;
            $total = max(0.00, $subtotal - $descuento);

            // Fetch ModoPago
            $modoPago = ModoPago::findOrFail($data['id_modo_pago']);

            // Create Boleta record
            $boleta = BoletaPago::create([
                'descuento' => $descuento,
                'fecha_emision' => now()->toDateString(),
                'total' => $total,
                'estado_pago' => 'PENDIENTE',
                'id_modo_pago' => $modoPago->id_modo_pago,
                'paciente_ci' => $data['paciente_ci'],
                'secretaria_ci' => $data['secretaria_ci'] ?? null,
            ]);

            // Link services to the bill
            foreach ($servicios as $s) {
                $s->update(['id_boleta' => $boleta->id_boleta]);
            }

            // Generate Installments
            if (strtoupper($modoPago->nombre) === 'CONTADO') {
                $boleta->cuotaBoleta()->create([
                    'numero_cuota' => 1,
                    'monto_cuota' => $total,
                    'fecha_vencimiento' => now()->toDateString(),
                    'estado' => 'PENDIENTE',
                ]);
            } else { // CREDITO
                $cantidadCuotas = (int) $data['cantidad_cuotas'];
                $tipoCredito = $data['tipo_credito']; // 'semanas' or 'meses'

                // Split amount equally
                $montoBase = round($total / $cantidadCuotas, 2);
                $sumaCuotas = $montoBase * $cantidadCuotas;
                $diferencia = $total - $sumaCuotas;

                for ($i = 1; $i <= $cantidadCuotas; $i++) {
                    $montoCuota = $montoBase;
                    if ($i === $cantidadCuotas) {
                        $montoCuota = round($montoCuota + $diferencia, 2); // adjust rounding remainder
                    }

                    // Calculate date (weeks = 7 days, months = 30 days)
                    if ($tipoCredito === 'semanas') {
                        $fechaVencimiento = now()->addDays(($i - 1) * 7)->toDateString();
                    } else { // meses
                        $fechaVencimiento = now()->addDays(($i - 1) * 30)->toDateString();
                    }

                    $boleta->cuotaBoleta()->create([
                        'numero_cuota' => $i,
                        'monto_cuota' => $montoCuota,
                        'fecha_vencimiento' => $fechaVencimiento,
                        'estado' => 'PENDIENTE',
                    ]);
                }
            }

            // Generate QR for the first installment (Cuota 1)
            $qrBase64 = null;
            $firstCuota = $boleta->cuotaBoleta()->where('numero_cuota', 1)->first();
            if ($firstCuota) {
                $pacienteModel = Paciente::with('persona')->findOrFail($data['paciente_ci']);
                $userModel = Auth::user();

                $qrData = [
                    'paymentMethod' => 34, // PagoFacil QR Code ID
                    'clientName' => $pacienteModel->persona->nombre . ' ' . $pacienteModel->persona->apellido,
                    'documentType' => 1, // CI
                    'documentId' => $pacienteModel->persona->carnet_identidad,
                    'phoneNumber' => $pacienteModel->persona->telefono ?? '70000000',
                    'email' => $userModel->email ?? 'correo@correo.com',
                    'paymentNumber' => $pacienteModel->persona->carnet_identidad . $firstCuota->id_cuota . Str::random(3),
                    'amount' => round($firstCuota->monto_cuota / 1000, 6), // PagoFacil expects amount / 1000
                    'currency' => 2, // 2 = Bs
                    'clientCode' => $userModel->codigo_usuario ?? 'GUEST',
                    'callbackUrl' => route('boleta_pago.callback', ['id_cuota' => $firstCuota->id_cuota]),
                    'orderDetail' => [
                        [
                            'serial' => 1,
                            'product' => 'Pago de la cuota Nro 1 de la boleta #' . $boleta->id_boleta,
                            'quantity' => 1,
                            'price' => round($firstCuota->monto_cuota / 1000, 6),
                            'discount' => 0,
                            'total' => round($firstCuota->monto_cuota / 1000, 6),
                        ]
                    ]
                ];

                $pagoFacilService = app(PagoFacilService::class);
                $response = $pagoFacilService->generarQR($qrData);

                if (isset($response['error']) && $response['error'] == 0 && isset($response['values']) && is_array($response['values']) && isset($response['values']['qrBase64'])) {
                    $qrBase64 = $response['values']['qrBase64'];
                    $firstCuota->update([
                        'id_transaccion' => $response['values']['transactionId']
                    ]);
                }
            }

            DB::commit();

            $redirect = redirect()->route('boleta_pago.index')
                ->with('success', 'Boleta de pago generada exitosamente. Escanee el QR para pagar la primera cuota.')
                ->with('flash_id', uniqid());

            if ($qrBase64) {
                $redirect = $redirect->with('qr_base64', $qrBase64)
                                     ->with('qr_cuota_id', $firstCuota->id_cuota);
            }

            return $redirect;

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('boleta_pago.index')
                ->with('error', 'Error al generar la boleta de pago: ' . $e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    /**
     * Record payment for a specific installment and calculate fines if late.
     */
    public function payCuota(PayCuotaRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $cuota = CuotaBoleta::where('id_boleta', $data['id_boleta'])
                ->where('numero_cuota', $data['numero_cuota'])
                ->firstOrFail();

            if ($cuota->estado === 'PAGADO') {
                throw new \Exception('Esta cuota ya ha sido cancelada anteriormente.');
            }

            // Check if any previous cuota is not paid or under review
            $hasUnpaidPrevious = CuotaBoleta::where('id_boleta', $cuota->id_boleta)
                ->where('numero_cuota', '<', $cuota->numero_cuota)
                ->whereNotIn('estado', ['PAGADO', 'REVISIÓN', 'REVISION'])
                ->exists();

            if ($hasUnpaidPrevious) {
                throw new \Exception('No se puede registrar el pago porque la cuota anterior no está en estado REVISIÓN o PAGADA.');
            }

            // Save upload file
            $path = null;
            if ($request->hasFile('comprobante')) {
                $path = $request->file('comprobante')->store('comprobantes', 'public');
            }

            // Set payment date
            $fechaPago = now()->toDateString();

            // Mark installment as paid
            $cuota->update([
                'fecha_pago' => $fechaPago,
                'estado' => 'PAGADO',
                'id_transaccion' => $data['id_transaccion'] ?? null,
                'comprobante' => $path,
                'id_metodo_pago' => $data['id_metodo_pago'],
            ]);

            // Late payment trigger logic (Multa 5% fixed)
            $dueDate = Carbon::parse($cuota->fecha_vencimiento);
            $payDate = Carbon::parse($fechaPago);

            if ($payDate->greaterThan($dueDate)) {
                $montoMulta = round($cuota->monto_cuota * 0.05, 2);
                $cuota->cuotaMulta()->create([
                    'monto_multa' => $montoMulta,
                    'fecha_generada' => $fechaPago,
                    'motivo' => "Multa por atraso en el pago de la cuota Nro {$cuota->numero_cuota} (Venció el {$cuota->fecha_vencimiento})",
                    'estado' => 'ACTIVO',
                ]);
            }

            // Update parent bill status
            $boleta = $cuota->boletaPago;
            $allPaid = !$boleta->cuotaBoleta()->where('estado', 'PENDIENTE')->exists();
            if ($allPaid) {
                $boleta->update(['estado_pago' => 'PAGADO']);
            } else {
                $boleta->update(['estado_pago' => 'PARCIAL']);
            }

            DB::commit();
            return redirect()->route('boleta_pago.index')
                ->with('success', 'Pago de cuota registrado exitosamente.')
                ->with('flash_id', uniqid());

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('boleta_pago.index')
                ->with('error', 'Error al procesar el pago de la cuota: ' . $e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    /**
     * Delete a billing sheet and cascade actions.
     */
    public function destroy(int $id_boleta)
    {
        DB::beginTransaction();
        try {
            $boleta = BoletaPago::findOrFail($id_boleta);

            if ($boleta->estado_pago === 'ELIMINADO') {
                throw new \Exception('Esta boleta de pago ya se encuentra eliminada.');
            }

            $hasRealPayments = $boleta->cuotaBoleta()
                ->whereNotIn('estado', ['PENDIENTE', 'ANULADO', 'ELIMINADO'])
                ->exists();

            if ($hasRealPayments) {
                throw new \Exception('No se puede eliminar la boleta de pago porque ya se ha registrado al menos un pago.');
            }

            // Dissociate associated services so they can be billed again
            $boleta->servicioPrestado()->update(['id_boleta' => null]);

            // Logical delete using estado_pago
            $boleta->update(['estado_pago' => 'ELIMINADO']);

            // Logical delete of installments
            $boleta->cuotaBoleta()->update(['estado' => 'ELIMINADO']);

            // Logical delete of installment fines
            foreach ($boleta->cuotaBoleta as $cuota) {
                $cuota->cuotaMulta()->update(['estado' => 'INACTIVO']);
            }

            DB::commit();
            return redirect()->route('boleta_pago.index')
                ->with('success', 'Boleta de pago eliminada correctamente.')
                ->with('flash_id', uniqid());

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('boleta_pago.index')
                ->with('error', 'Error al eliminar la boleta de pago: ' . $e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    /**
     * Generate QR code for a specific installment on-demand.
     */
    public function generarQrCuota(int $id_cuota, PagoFacilService $pagoFacilService)
    {
        try {
            $cuota = CuotaBoleta::with('boletaPago.paciente.persona')->findOrFail($id_cuota);
            if ($cuota->estado === 'PAGADO') {
                return response()->json(['success' => false, 'message' => 'Esta cuota ya está pagada.']);
            }

            // Check if any previous cuota is not paid or under review
            $hasUnpaidPrevious = CuotaBoleta::where('id_boleta', $cuota->id_boleta)
                ->where('numero_cuota', '<', $cuota->numero_cuota)
                ->whereNotIn('estado', ['PAGADO', 'REVISIÓN', 'REVISION'])
                ->exists();

            if ($hasUnpaidPrevious) {
                return response()->json(['success' => false, 'message' => 'No se puede generar el código QR porque la cuota anterior no está en estado REVISIÓN o PAGADA.']);
            }

            $paciente = $cuota->boletaPago->paciente;
            $user = Auth::user();

            $qrData = [
                'paymentMethod' => 34,
                'clientName' => $paciente->persona->nombre . ' ' . $paciente->persona->apellido,
                'documentType' => 1,
                'documentId' => $paciente->persona->carnet_identidad,
                'phoneNumber' => $paciente->persona->telefono ?? '70000000',
                'email' => $user->email ?? 'correo@correo.com',
                'paymentNumber' => $paciente->persona->carnet_identidad . $cuota->id_cuota . Str::random(3),
                'amount' => round($cuota->monto_cuota / 1000, 6),
                'currency' => 2, // 2 = Bs
                'clientCode' => $user->codigo_usuario ?? 'GUEST',
                'callbackUrl' => "https://tecnoweb.org.bo/inf513/grupo04sa/sa-proyecto-via-web-grupo04sa/odontocool/public/boleta-pago/callback/1",
                'orderDetail' => [
                    [
                        'serial' => 1,
                        'product' => 'Pago de la cuota Nro ' . $cuota->numero_cuota . ' de la boleta #' . $cuota->id_boleta,
                        'quantity' => 1,
                        'price' => round($cuota->monto_cuota / 1000, 6),
                        'discount' => 0,
                        'total' => round($cuota->monto_cuota / 1000, 6),
                    ]
                ]
            ];

            $response = $pagoFacilService->generarQR($qrData);

            if (is_array($response) && isset($response['error']) && $response['error'] == 0 && isset($response['values']) && is_array($response['values']) && isset($response['values']['qrBase64'])) {
                $cuota->update([
                    'id_transaccion' => $response['values']['transactionId']
                ]);

                return response()->json([
                    'success' => true,
                    'qr_base64' => $response['values']['qrBase64'],
                    'transaction_id' => $response['values']['transactionId'],
                    'checkout_url' => $response['values']['checkoutUrl'] ?? null,
                ]);
            }

            $errorMessage = 'Error desconocido al generar el código QR.';
            if (is_array($response) && isset($response['message'])) {
                $errorMessage = $response['message'];
            } elseif (is_string($response)) {
                $errorMessage = $response;
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Query and verify transaction status from PagoFacil manually.
     */
    public function verificarPagoCuota(int $id_cuota, PagoFacilService $pagoFacilService)
    {
        try {
            $cuota = CuotaBoleta::findOrFail($id_cuota);
            if ($cuota->estado === 'PAGADO') {
                return redirect()
                    ->back()
                    ->with('success', 'La cuota ya está pagada.')
                    ->with('estado', 'PAGADO')
                    ->with('flash_id', uniqid());
            }

            if (empty($cuota->id_transaccion)) {
                return redirect()
                    ->back()
                    ->with('error', 'No hay una transacción QR activa para esta cuota. Genere el QR primero.')
                    ->with('flash_id', uniqid());
            }

            $response = $pagoFacilService->consultarEstadoTransaccion($cuota->id_transaccion);

            if (is_array($response) && isset($response['error']) && $response['error'] == 0 && isset($response['values']) && is_array($response['values'])) {
                $estadoTransaccion = $response['values']['paymentStatus'] ?? null;

                if ($estadoTransaccion == 2 || strtoupper($response['values']['paymentStatusDescription'] ?? '') === 'COMPLETED' || strtoupper($response['values']['paymentStatusDescription'] ?? '') === 'PROCESADO' || strtoupper($response['values']['paymentStatusDescription'] ?? '') === 'PAGADO') {
                    DB::beginTransaction();
                    $fechaPago = now()->toDateString();
                    $cuota->update([
                        'fecha_pago' => $fechaPago,
                        'estado' => 'PAGADO',
                        'comprobante' => 'PAGO_QR_PAGOFACIL',
                        'id_metodo_pago' => MetodoPago::where('nombre', 'QR')->first()->id_metodo_pago ?? $cuota->id_metodo_pago,
                    ]);

                    $dueDate = Carbon::parse($cuota->fecha_vencimiento);
                    $payDate = Carbon::parse($fechaPago);

                    if ($payDate->greaterThan($dueDate)) {
                        $montoMulta = round($cuota->monto_cuota * 0.05, 2);
                        $cuota->cuotaMulta()->create([
                            'monto_multa' => $montoMulta,
                            'fecha_generada' => $fechaPago,
                            'motivo' => "Multa por atraso en el pago de la cuota Nro {$cuota->numero_cuota} (Venció el {$cuota->fecha_vencimiento})",
                            'estado' => 'ACTIVO',
                        ]);
                    }

                    $boleta = $cuota->boletaPago;
                    $allPaid = !$boleta->cuotaBoleta()->where('estado', 'PENDIENTE')->exists();
                    if ($allPaid) {
                        $boleta->update(['estado_pago' => 'PAGADO']);
                    } else {
                        $boleta->update(['estado_pago' => 'PARCIAL']);
                    }
                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('success', '¡Pago verificado y procesado exitosamente!')
                        ->with('estado', 'PAGADO')
                        ->with('flash_id', uniqid());
                }

                $statusDescription = 'PENDIENTE';
                if (is_array($response['values'] ?? null)) {
                    $statusDescription = $response['values']['paymentStatusDescription'] ?? 'PENDIENTE';
                }

                $nuevoEstado = mb_strtoupper($statusDescription, 'UTF-8');
                if (str_replace(['ó', 'o', 'Ó', 'O'], 'O', strtolower($nuevoEstado)) === 'revision') {
                    $nuevoEstado = 'REVISIÓN';
                }

                DB::beginTransaction();
                $cuota->update([
                    'estado' => $nuevoEstado
                ]);
                DB::commit();

                return redirect()
                    ->back()
                    ->with('warning', 'El pago aún no ha sido completado. Estado actual: ' . $nuevoEstado)
                    ->with('estado', $nuevoEstado)
                    ->with('flash_id', uniqid());
            }

            $errorMessage = 'No se pudo consultar el estado del pago.';
            if (is_array($response) && isset($response['message'])) {
                $errorMessage = $response['message'];
            } elseif (is_string($response)) {
                $errorMessage = $response;
            }

            return redirect()
                ->back()
                ->with('error', $errorMessage)
                ->with('flash_id', uniqid());

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->with('flash_id', uniqid());
        }
    }

    /**
     * PagoFacil Webhook callback receiver.
     */
    public function callback(int $id_cuota, Request $request, PagoFacilService $pagoFacilService)
    {
        \Illuminate\Support\Facades\Log::info('PagoFacil Callback received', [
            'id_cuota' => $id_cuota,
            'data' => $request->all()
        ]);

        DB::beginTransaction();
        try {
            $cuota = CuotaBoleta::findOrFail($id_cuota);
            if ($cuota->estado === 'PAGADO') {
                return response()->json(['success' => true, 'message' => 'La cuota ya está pagada.']);
            }

            $transaccionId = $cuota->id_transaccion ?? $request->input('values.transactionId') ?? $request->input('transactionId');
            if (empty($transaccionId)) {
                throw new \Exception('No transaction ID associated with this installment.');
            }

            $response = $pagoFacilService->consultarEstadoTransaccion($transaccionId);

            if (isset($response['error']) && $response['error'] == 0 && isset($response['values']) && is_array($response['values'])) {
                $estadoTransaccion = $response['values']['paymentStatus'] ?? null;

                if ($estadoTransaccion == 2 || strtoupper($response['values']['paymentStatusDescription'] ?? '') === 'COMPLETED' || strtoupper($response['values']['paymentStatusDescription'] ?? '') === 'PROCESADO' || strtoupper($response['values']['paymentStatusDescription'] ?? '') === 'PAGADO') {
                    $fechaPago = now()->toDateString();
                    $cuota->update([
                        'fecha_pago' => $fechaPago,
                        'estado' => 'PAGADO',
                        'comprobante' => 'PAGO_QR_PAGOFACIL',
                        'id_metodo_pago' => MetodoPago::where('nombre', 'QR')->first()->id_metodo_pago ?? $cuota->id_metodo_pago,
                    ]);

                    $dueDate = Carbon::parse($cuota->fecha_vencimiento);
                    $payDate = Carbon::parse($fechaPago);

                    if ($payDate->greaterThan($dueDate)) {
                        $montoMulta = round($cuota->monto_cuota * 0.05, 2);
                        $cuota->cuotaMulta()->create([
                            'monto_multa' => $montoMulta,
                            'fecha_generada' => $fechaPago,
                            'motivo' => "Multa por atraso en el pago de la cuota Nro {$cuota->numero_cuota} (Venció el {$cuota->fecha_vencimiento})",
                            'estado' => 'ACTIVO',
                        ]);
                    }

                    $boleta = $cuota->boletaPago;
                    $allPaid = !$boleta->cuotaBoleta()->where('estado', 'PENDIENTE')->exists();
                    if ($allPaid) {
                        $boleta->update(['estado_pago' => 'PAGADO']);
                    } else {
                        $boleta->update(['estado_pago' => 'PARCIAL']);
                    }
                    DB::commit();

                    \Illuminate\Support\Facades\Log::info('PagoFacil Callback successfully processed cuota payment', [
                        'id_cuota' => $id_cuota
                    ]);

                    return response()->json(['success' => true, 'message' => 'Pago procesado por callback.']);
                }
            }

            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'El pago no se encuentra en estado completado en PagoFacil.']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error processing PagoFacil callback', [
                'id_cuota' => $id_cuota,
                'message' => $e->getMessage()
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
