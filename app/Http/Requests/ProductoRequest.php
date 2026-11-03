<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'categoria' => ['required', 'string', 'max:100'],
            'precio' => ['required', 'numeric', 'gt:0', 'max:99999999.99'],
            'stock' => ['required', 'integer', 'min:0'],
            'proveedor_id' => ['required', 'integer', 'exists:proveedores,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.max' => 'El nombre no debe superar los 150 caracteres.',
            'categoria.required' => 'La categoría es obligatoria.',
            'categoria.max' => 'La categoría no debe superar los 100 caracteres.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un valor numérico.',
            'precio.gt' => 'El precio debe ser mayor que cero.',
            'precio.max' => 'El precio no debe superar los 99 999 999.99 soles.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser negativo.',
            'proveedor_id.required' => 'Debes seleccionar un proveedor.',
            'proveedor_id.integer' => 'El proveedor seleccionado no es válido.',
            'proveedor_id.exists' => 'El proveedor seleccionado no existe.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'descripcion' => 'descripción',
            'categoria' => 'categoría',
            'precio' => 'precio',
            'stock' => 'stock',
            'proveedor_id' => 'proveedor',
        ];
    }
}
