<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PagoFacilService
{
    protected string $url;
    protected string $token;

    public function __construct()
    {
        $this->url = config('services.pagofacil.url', 'https://api.pagofacil.com.bo');
        $this->token = config('services.pagofacil.token') ?? '';
    }

    /**
     * Generate QR Code from PagoFacil.
     */
    public function generarQR(array $data): array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                'Accept' => 'application/json',
            ])->withToken($this->token)
                ->post($this->url . '/generate-qr', $data);

            if ($response->failed()) {
                Log::error('PagoFacil API generate-qr failure', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                $message = 'Error al conectar con Pago Fácil.';
                try {
                    $json = $response->json();
                    if (is_array($json) && isset($json['message'])) {
                        $message = $json['message'];
                    } elseif (is_string($json)) {
                        $message = $json;
                    } else {
                        $message = $response->body();
                    }
                } catch (\Exception $ex) {
                    $message = $response->body();
                }

                return [
                    'error' => 1,
                    'message' => 'Error al conectar con Pago Fácil: ' . $message,
                ];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('PagoFacil API generate-qr exception', [
                'message' => $e->getMessage()
            ]);
            return [
                'error' => 1,
                'message' => 'Excepción al conectar con Pago Fácil: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Query transaction status from PagoFacil.
     */
    public function consultarEstadoTransaccion(string $transaccionId): array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                'Accept' => 'application/json',
            ])->withToken($this->token)
                ->post($this->url . '/query-transaction', [
                    'pagofacilTransactionId' => (int) $transaccionId
                ]);

            if ($response->failed()) {
                Log::error('PagoFacil API query-transaction failure', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                $message = 'Error al consultar estado en Pago Fácil.';
                try {
                    $json = $response->json();
                    if (is_array($json) && isset($json['message'])) {
                        $message = $json['message'];
                    } elseif (is_string($json)) {
                        $message = $json;
                    } else {
                        $message = $response->body();
                    }
                } catch (\Exception $ex) {
                    $message = $response->body();
                }

                return [
                    'error' => 1,
                    'message' => 'Error al consultar estado en Pago Fácil: ' . $message,
                ];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('PagoFacil API query-transaction exception', [
                'message' => $e->getMessage()
            ]);
            return [
                'error' => 1,
                'message' => 'Excepción al consultar estado en Pago Fácil: ' . $e->getMessage(),
            ];
        }
    }
}
