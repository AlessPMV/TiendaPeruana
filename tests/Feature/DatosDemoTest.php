<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Venta;
use Database\Seeders\CatalogoDemoSeeder;
use Database\Seeders\ClientesVentasDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatosDemoTest extends TestCase
{
    use RefreshDatabase;

    public function test_los_datos_demo_se_cargan_sin_duplicarse(): void
    {
        $this->seed(CatalogoDemoSeeder::class);
        $this->seed(ClientesVentasDemoSeeder::class);
        $this->seed(ClientesVentasDemoSeeder::class);

        $this->assertSame(8, Producto::query()->count());
        $this->assertSame(6, Cliente::query()->count());
        $this->assertSame(8, Venta::query()->count());
        $this->assertSame(20, DetalleVenta::query()->count());

        foreach (Venta::query()->with('detalleVentas')->get() as $venta) {
            $this->assertEqualsWithDelta(
                (float) $venta->total,
                (float) $venta->detalleVentas->sum('subtotal'),
                0.001
            );
        }

        foreach (Cliente::query()->get() as $cliente) {
            $this->assertMatchesRegularExpression('/^\d{8}$/', $cliente->documento);
        }

        $clientes = $this->get(route('clientes.index'));
        $clientes->assertOk();
        $clientes->assertSee('6 clientes registrados');
        $clientes->assertSee('Rosa Elena');

        $ventas = $this->get(route('ventas.index'));
        $ventas->assertOk();
        $ventas->assertSee('8 ventas registradas');
        $ventas->assertSee('S/ 108.80');
    }
}
