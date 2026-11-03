<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistroVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => ['required', 'integer', 'exists:clientes,id'],
            'productos' => ['required', 'array', 'min:1'],
            'productos.*.producto_id' => ['required', 'integer', 'distinct', 'exists:productos,id'],
            'productos.*.cantidad' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'Debes seleccionar un cliente.',
            'cliente_id.integer' => 'El cliente seleccionado no es válido.',
            'cliente_id.exists' => 'El cliente seleccionado no existe.',
            'productos.required' => 'Debes agregar al menos un producto.',
            'productos.array' => 'Los productos enviados no son válidos.',
            'productos.min' => 'Debes agregar al menos un producto.',
            'productos.*.producto_id.required' => 'Debes seleccionar un producto en cada fila.',
            'productos.*.producto_id.integer' => 'El producto seleccionado no es válido.',
            'productos.*.producto_id.distinct' => 'No puedes repetir el mismo producto en la venta.',
            'productos.*.producto_id.exists' => 'El producto seleccionado no existe.',
            'productos.*.cantidad.required' => 'Debes ingresar la cantidad.',
            'productos.*.cantidad.integer' => 'La cantidad debe ser un número entero.',
            'productos.*.cantidad.min' => 'La cantidad debe ser mayor que cero.',
        ];
    }

    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'productos' => 'productos',
            'productos.*.producto_id' => 'producto',
            'productos.*.cantidad' => 'cantidad',
        ];
    }
}
