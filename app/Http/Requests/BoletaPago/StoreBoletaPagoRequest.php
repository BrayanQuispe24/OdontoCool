<?php

namespace App\Http\Requests\BoletaPago;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ModoPago;

class StoreBoletaPagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'descuento' => ['nullable', 'numeric', 'min:0'],
            'id_modo_pago' => ['required', 'exists:modo_pago,id_modo_pago'],
            'paciente_ci' => ['required', 'exists:pacientes,codigo_paciente'],
            'secretaria_ci' => ['nullable', 'exists:secretarias,codigo_secretaria'],
            'servicio_prestado_ids' => ['required', 'array'],
            'servicio_prestado_ids.*' => ['required', 'integer', 'exists:servicio_prestados,id'],
            
            // Credit attributes (conditional validation)
            'tipo_credito' => ['required_if:id_modo_pago,' . $this->getCreditModeId(), 'in:semanas,meses'],
            'cantidad_cuotas' => ['required_if:id_modo_pago,' . $this->getCreditModeId(), 'integer', 'min:1'],
        ];
    }

    private function getCreditModeId(): ?int
    {
        $mode = ModoPago::where('nombre', 'CREDITO')->first();
        return $mode ? $mode->id_modo_pago : null;
    }
}
