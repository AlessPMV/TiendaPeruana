<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientesVentasDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $clientes = $this->crearClientes();
            $productos = Producto::query()->get()->keyBy('nombre');

            foreach ($this->ventasDemo() as $datosVenta) {
                $cliente = $clientes[$datosVenta['cliente']];
                $detalles = [];
                $total = 0.0;

                foreach ($datosVenta['productos'] as $nombre => $cantidad) {
                    $producto = $productos->get($nombre);

                    if (! $producto) {
                        continue;
                    }

                    $precio = round((float) $producto->precio, 2);
                    $subtotal = round($precio * $cantidad, 2);
                    $total += $subtotal;

                    $detalles[] = [
                        'producto_id' => $producto->id,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio,
                        'subtotal' => $subtotal,
                    ];
                }

                if ($detalles === []) {
                    continue;
                }

                $venta = Venta::query()
                    ->where('cliente_id', $cliente->id)
                    ->whereDate('fecha', $datosVenta['fecha'])
                    ->first() ?? new Venta();

                $venta->fill([
                    'cliente_id' => $cliente->id,
                    'fecha' => $datosVenta['fecha'],
                    'total' => round($total, 2),
                ])->save();

                $venta->detalleVentas()
                    ->whereNotIn('producto_id', array_column($detalles, 'producto_id'))
                    ->delete();

                foreach ($detalles as $detalle) {
                    $venta->detalleVentas()->updateOrCreate(
                        ['producto_id' => $detalle['producto_id']],
                        $detalle
                    );
                }
            }
        });
    }

    private function crearClientes(): array
    {
        $datos = [
            'rosa' => [
                'nombres' => 'Rosa Elena',
                'apellidos' => 'Huamán Quispe',
                'documento' => '45879213',
                'telefono' => '987654321',
                'email' => 'rosa.huaman@example.test',
                'direccion' => 'Av. Los Álamos 245, San Juan de Lurigancho, Lima',
            ],
            'carlos' => [
                'nombres' => 'Carlos Alberto',
                'apellidos' => 'Mendoza Rojas',
                'documento' => '71234568',
                'telefono' => '912345678',
                'email' => 'carlos.mendoza@example.test',
                'direccion' => 'Jr. Puno 812, Cercado de Lima',
            ],
            'ana' => [
                'nombres' => 'Ana Lucía',
                'apellidos' => 'Torres Salazar',
                'documento' => '73456182',
                'telefono' => '998877665',
                'email' => 'ana.torres@example.test',
                'direccion' => 'Av. Larco 1234, Miraflores, Lima',
            ],
            'jorge' => [
                'nombres' => 'Jorge Luis',
                'apellidos' => 'Paredes Cárdenas',
                'documento' => '40987654',
                'telefono' => '955443322',
                'email' => 'jorge.paredes@example.test',
                'direccion' => 'Calle Los Nogales 78, Santiago de Surco, Lima',
            ],
            'maria' => [
                'nombres' => 'María Fernanda',
                'apellidos' => 'Chávez Ríos',
                'documento' => '76543210',
                'telefono' => '944556677',
                'email' => 'maria.chavez@example.test',
                'direccion' => 'Av. Arequipa 2450, Lince, Lima',
            ],
            'pedro' => [
                'nombres' => 'Pedro Pablo',
                'apellidos' => 'Castillo Vega',
                'documento' => '44556677',
                'telefono' => '933221100',
                'email' => 'pedro.castillo@example.test',
                'direccion' => 'Jr. Amazonas 368, Jesús María, Lima',
            ],
        ];

        $clientes = [];

        foreach ($datos as $clave => $cliente) {
            $clientes[$clave] = Cliente::query()->updateOrCreate(
                ['documento' => $cliente['documento']],
                $cliente
            );
        }

        return $clientes;
    }

    private function ventasDemo(): array
    {
        return [
            [
                'cliente' => 'rosa',
                'fecha' => '2026-10-24',
                'productos' => [
                    'Arroz extra 5 kg' => 2,
                    'Leche evaporada 400 g' => 4,
                ],
            ],
            [
                'cliente' => 'carlos',
                'fecha' => '2026-10-26',
                'productos' => [
                    'Aceite vegetal 1 L' => 1,
                    'Fideos spaghetti 500 g' => 3,
                    'Galletas de soda' => 2,
                ],
            ],
            [
                'cliente' => 'ana',
                'fecha' => '2026-10-28',
                'productos' => [
                    'Detergente 1 kg' => 1,
                    'Agua mineral 625 ml' => 6,
                ],
            ],
            [
                'cliente' => 'jorge',
                'fecha' => '2026-10-30',
                'productos' => [
                    'Yogur natural 1 L' => 2,
                    'Leche evaporada 400 g' => 2,
                ],
            ],
            [
                'cliente' => 'maria',
                'fecha' => '2026-11-01',
                'productos' => [
                    'Arroz extra 5 kg' => 1,
                    'Aceite vegetal 1 L' => 2,
                    'Agua mineral 625 ml' => 4,
                ],
            ],
            [
                'cliente' => 'pedro',
                'fecha' => '2026-11-01',
                'productos' => [
                    'Fideos spaghetti 500 g' => 2,
                    'Galletas de soda' => 3,
                    'Leche evaporada 400 g' => 2,
                ],
            ],
            [
                'cliente' => 'rosa',
                'fecha' => '2026-11-02',
                'productos' => [
                    'Detergente 1 kg' => 2,
                    'Yogur natural 1 L' => 1,
                ],
            ],
            [
                'cliente' => 'ana',
                'fecha' => '2026-11-02',
                'productos' => [
                    'Arroz extra 5 kg' => 3,
                    'Leche evaporada 400 g' => 6,
                    'Aceite vegetal 1 L' => 1,
                ],
            ],
        ];
    }
}
