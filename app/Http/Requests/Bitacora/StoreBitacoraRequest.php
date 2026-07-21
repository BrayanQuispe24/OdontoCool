<?php

namespace App\Http\Requests\Bitacora;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBitacoraRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'persona_id' => ['nullable', 'string', 'max:10', 'exists:personas,carnet_identidad'],
            'accion' => ['required', 'string', 'max:255'],
            'modulo' => ['required', 'string', 'max:100'],
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'string'],
            'ip_address' => ['nullable', 'ip'],
            'user_agent' => ['nullable', 'string'],
            'detalles' => ['nullable', 'array'],
        ];
    }
}
