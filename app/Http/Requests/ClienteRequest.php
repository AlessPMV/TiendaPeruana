<?php

namespace App\Http\Requests;

use App\Models\Cliente;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $cliente = $this->route('cliente');
        $clienteId = $cliente instanceof Cliente ? $cliente->id : null;

        return [
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'documento' => [
                'required',
                'string',
                'max:20',
                Rule::unique('clientes', 'documento')->ignore($clienteId),
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
                Rule::unique('clientes', 'email')->ignore($clienteId),
            ],
            'direccion' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombres.required' => 'Los nombres son obligatorios.',
            'nombres.max' => 'Los nombres no deben superar los 100 caracteres.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.max' => 'Los apellidos no deben superar los 100 caracteres.',
            'documento.required' => 'El documento es obligatorio.',
            'documento.max' => 'El documento no debe superar los 20 caracteres.',
            'documento.unique' => 'Ya existe un cliente registrado con este documento.',
            'telefono.min' => 'El teléfono debe tener al menos 6 caracteres.',
            'telefono.max' => 'El teléfono no debe superar los 20 caracteres.',
            'telefono.regex' => 'El teléfono solo puede contener números, espacios, paréntesis, guiones y el signo +.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.max' => 'El correo electrónico no debe superar los 150 caracteres.',
            'email.unique' => 'Ya existe un cliente registrado con este correo electrónico.',
            'direccion.max' => 'La dirección no debe superar los 255 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombres' => 'nombres',
            'apellidos' => 'apellidos',
            'documento' => 'documento',
            'telefono' => 'teléfono',
            'email' => 'correo electrónico',
            'direccion' => 'dirección',
        ];
    }
}
