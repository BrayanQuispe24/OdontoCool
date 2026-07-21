<?php

namespace App\Http\Requests\Turno;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTurnoRequest extends FormRequest
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
            'nombre' => ['sometimes','required','string','max:50'],
            'estado' => ['sometimes','required','string','max:20',Rule::in(['activo', 'inactivo','suspendido'])],
            'hora_inicio' => ['sometimes','required'],
            'hora_fin' => ['sometimes','required','after:hora_inicio'],
        ];
    }
}
