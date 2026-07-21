<?php

namespace App\Http\Requests\BoletaPago;

use Illuminate\Foundation\Http\FormRequest;

class PayCuotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_boleta' => ['required', 'exists:boleta_pago,id_boleta'],
            'numero_cuota' => ['required', 'integer'],
            'id_metodo_pago' => ['required', 'exists:metodo_pago,id_metodo_pago'],
            'comprobante' => ['required', 'file', 'image', 'max:2048'], // image only, max 2MB
            'id_transaccion' => ['nullable', 'string', 'max:100'],
        ];
    }
}
