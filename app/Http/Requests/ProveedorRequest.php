<?php

namespace App\Http\Requests;

use App\Models\Proveedor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $proveedor = $this->route('proveedor');
        $proveedorId = $proveedor instanceof Proveedor ? $proveedor->id : null;

        return [
            'nombre' => ['required', 'string', 'max:150'],
            'ruc' => [
                'required',
                'string',
                'max:20',
                Rule::unique('proveedores', 'ruc')->ignore($proveedorId),
            ],
            'telefono' => [
                'nullable',
                'string',
                'min:6',
                'max:20',
                'regex:/^[0-9+\-()\s]+$/',
            ],
            'email' => [
                'nullable',
                'email',
                'max:150',
                Rule::unique('proveedores', 'email')->ignore($proveedorId),
            ],
            'direccion' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del proveedor es obligatorio.',
            'nombre.max' => 'El nombre no debe superar los 150 caracteres.',
            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.max' => 'El RUC no debe superar los 20 caracteres.',
            'ruc.unique' => 'Ya existe un proveedor registrado con este RUC.',
            'telefono.min' => 'El teléfono debe tener al menos 6 caracteres.',
            'telefono.max' => 'El teléfono no debe superar los 20 caracteres.',
            'telefono.regex' => 'El teléfono solo puede contener números, espacios, paréntesis, guiones y el signo +.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.max' => 'El correo electrónico no debe superar los 150 caracteres.',
            'email.unique' => 'Ya existe un proveedor registrado con este correo electrónico.',
            'direccion.max' => 'La dirección no debe superar los 255 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'ruc' => 'RUC',
            'telefono' => 'teléfono',
            'email' => 'correo electrónico',
            'direccion' => 'dirección',
        ];
    }
}
