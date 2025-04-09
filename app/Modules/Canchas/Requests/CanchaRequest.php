<?php

namespace App\Modules\Canchas\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CanchaRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'capacidad' => 'required|integer|min:1',
            'precio_hora' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'estado' => 'nullable|in:disponible,ocupada,mantenimiento'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la cancha es obligatorio',
            'nombre.max' => 'El nombre no puede exceder los 255 caracteres',
            'ubicacion.required' => 'La ubicación de la cancha es obligatoria',
            'ubicacion.max' => 'La ubicación no puede exceder los 255 caracteres',
            'capacidad.required' => 'La capacidad de la cancha es obligatoria',
            'capacidad.integer' => 'La capacidad debe ser un número entero',
            'capacidad.min' => 'La capacidad debe ser al menos 1',
            'precio_hora.required' => 'El precio por hora es obligatorio',
            'precio_hora.numeric' => 'El precio por hora debe ser un número',
            'precio_hora.min' => 'El precio por hora no puede ser negativo',
            'estado.in' => 'El estado debe ser: disponible, ocupada o mantenimiento'
        ];
    }
}
