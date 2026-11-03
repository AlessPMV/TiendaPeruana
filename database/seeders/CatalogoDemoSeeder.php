<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class CatalogoDemoSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = [
            'andina' => Proveedor::query()->updateOrCreate(
                ['ruc' => '20512345671'],
                [
                    'nombre' => 'Distribuidora Andina S.A.C.',
                    'telefono' => '014567891',
                    'email' => 'ventas@andina.example.test',
                    'direccion' => 'Av. Los Andes 120, Lima',
                ]
            ),
            'pacifico' => Proveedor::query()->updateOrCreate(
                ['ruc' => '20512345672'],
                [
                    'nombre' => 'Alimentos del Pacífico S.A.C.',
                    'telefono' => '014567892',
                    'email' => 'contacto@pacifico.example.test',
                    'direccion' => 'Av. del Puerto 450, Callao',
                ]
            ),
            'limpieza' => Proveedor::query()->updateOrCreate(
                ['ruc' => '20512345673'],
                [
                    'nombre' => 'Limpieza Total S.A.C.',
                    'telefono' => '014567893',
                    'email' => 'ventas@limpiezatotal.example.test',
                    'direccion' => 'Jr. Comercio 890, Lima',
                ]
            ),
        ];

        $productos = [
            [
                'nombre' => 'Arroz extra 5 kg',
                'descripcion' => 'Bolsa de arroz extra de grano largo, presentación de 5 kilogramos.',
                'categoria' => 'Abarrotes',
                'precio' => 24.90,
                'stock' => 40,
                'proveedor_id' => $proveedores['andina']->id,
            ],
            [
                'nombre' => 'Leche evaporada 400 g',
                'descripcion' => 'Leche evaporada entera en tarro de 400 gramos.',
                'categoria' => 'Lácteos',
                'precio' => 4.20,
                'stock' => 60,
                'proveedor_id' => $proveedores['pacifico']->id,
            ],
            [
                'nombre' => 'Aceite vegetal 1 L',
                'descripcion' => 'Aceite vegetal comestible en botella de 1 litro.',
                'categoria' => 'Abarrotes',
                'precio' => 8.90,
                'stock' => 35,
                'proveedor_id' => $proveedores['andina']->id,
            ],
            [
                'nombre' => 'Agua mineral 625 ml',
                'descripcion' => 'Agua mineral sin gas en botella de 625 mililitros.',
                'categoria' => 'Bebidas',
                'precio' => 1.50,
                'stock' => 120,
                'proveedor_id' => $proveedores['pacifico']->id,
            ],
            [
                'nombre' => 'Detergente 1 kg',
                'descripcion' => 'Detergente en polvo para ropa, bolsa de 1 kilogramo.',
                'categoria' => 'Limpieza',
                'precio' => 12.50,
                'stock' => 25,
                'proveedor_id' => $proveedores['limpieza']->id,
            ],
            [
                'nombre' => 'Galletas de soda',
                'descripcion' => 'Paquete de galletas de soda de consumo familiar.',
                'categoria' => 'Snacks',
                'precio' => 2.30,
                'stock' => 80,
                'proveedor_id' => $proveedores['pacifico']->id,
            ],
            [
                'nombre' => 'Yogur natural 1 L',
                'descripcion' => 'Yogur natural sin azúcar añadida, botella de 1 litro.',
                'categoria' => 'Lácteos',
                'precio' => 6.90,
                'stock' => 30,
                'proveedor_id' => $proveedores['pacifico']->id,
            ],
            [
                'nombre' => 'Fideos spaghetti 500 g',
                'descripcion' => 'Fideos de sémola de trigo, presentación de 500 gramos.',
                'categoria' => 'Abarrotes',
                'precio' => 3.80,
                'stock' => 50,
                'proveedor_id' => $proveedores['andina']->id,
            ],
        ];

        foreach ($productos as $producto) {
            Producto::query()->updateOrCreate(
                ['nombre' => $producto['nombre']],
                $producto
            );
        }
    }
}
