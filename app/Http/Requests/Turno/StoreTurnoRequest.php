<?php

namespace App\Http\Requests\Turno;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTurnoRequest extends FormRequest
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
            'nombre' => ['required','string','max:50'],
            //'estado' => ['required','string','max:20'],
            'hora_inicio' => ['required','date_format:H:i'],
            'hora_fin' => ['required','date_format:H:i','after:hora_inicio'],
        ];
    }
}
