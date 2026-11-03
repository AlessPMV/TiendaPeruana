<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Venta;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_models_expose_the_expected_relationships(): void
    {
        $proveedor = $this->crearProveedor();
        $producto = $this->crearProducto($proveedor);
        $cliente = $this->crearCliente();
        $venta = $this->crearVenta($cliente);
        $detalle = $this->crearDetalle($venta, $producto);

        $this->assertTrue($proveedor->productos->contains($producto));
        $this->assertTrue($producto->proveedor->is($proveedor));
        $this->assertTrue($cliente->ventas->contains($venta));
        $this->assertTrue($venta->cliente->is($cliente));
        $this->assertTrue($venta->detalleVentas->contains($detalle));
        $this->assertTrue($detalle->venta->is($venta));
        $this->assertTrue($producto->detalleVentas->contains($detalle));
        $this->assertTrue($detalle->producto->is($producto));
    }

    public function test_deleting_a_sale_cascades_its_details(): void
    {
        $proveedor = $this->crearProveedor();
        $producto = $this->crearProducto($proveedor);
        $cliente = $this->crearCliente();
        $venta = $this->crearVenta($cliente);
        $detalle = $this->crearDetalle($venta, $producto);

        $venta->delete();

        $this->assertDatabaseMissing('detalle_ventas', ['id' => $detalle->id]);
        $this->assertDatabaseHas('clientes', ['id' => $cliente->id]);
        $this->assertDatabaseHas('productos', ['id' => $producto->id]);
    }

    public function test_a_provider_with_products_cannot_be_deleted(): void
    {
        $proveedor = $this->crearProveedor();
        $this->crearProducto($proveedor);

        $this->expectException(QueryException::class);

        $proveedor->delete();
    }

    public function test_a_client_with_sales_cannot_be_deleted(): void
    {
        $cliente = $this->crearCliente();
        $this->crearVenta($cliente);

        $this->expectException(QueryException::class);

        $cliente->delete();
    }

    public function test_a_product_used_in_a_sale_cannot_be_deleted(): void
    {
        $proveedor = $this->crearProveedor();
        $producto = $this->crearProducto($proveedor);
        $cliente = $this->crearCliente();
        $venta = $this->crearVenta($cliente);
        $this->crearDetalle($venta, $producto);

        $this->expectException(QueryException::class);

        $producto->delete();
    }

    private function crearProveedor(): Proveedor
    {
        return Proveedor::create([
            'nombre' => 'Distribuidora Los Andes',
            'ruc' => '20123456789',
            'telefono' => '987654321',
            'email' => 'ventas@losandes.test',
            'direccion' => 'Av. Comercial 123, Lima',
        ]);
    }

    private function crearProducto(Proveedor $proveedor): Producto
    {
        return Producto::create([
            'nombre' => 'Arroz extra 5 kg',
            'descripcion' => 'Bolsa de arroz ficticia para pruebas',
            'categoria' => 'Abarrotes',
            'precio' => 24.90,
            'stock' => 40,
            'proveedor_id' => $proveedor->id,
        ]);
    }

    private function crearCliente(): Cliente
    {
        return Cliente::create([
            'nombres' => 'María Elena',
            'apellidos' => 'Quispe Ramos',
            'documento' => '12345678',
            'telefono' => '912345678',
            'email' => 'maria.quispe@example.test',
            'direccion' => 'Jr. Las Flores 456, Lima',
        ]);
    }

    private function crearVenta(Cliente $cliente): Venta
    {
        return Venta::create([
            'cliente_id' => $cliente->id,
            'fecha' => '2026-11-02',
            'total' => 24.90,
        ]);
    }

    private function crearDetalle(Venta $venta, Producto $producto): DetalleVenta
    {
        return DetalleVenta::create([
            'venta_id' => $venta->id,
            'producto_id' => $producto->id,
            'cantidad' => 1,
            'precio_unitario' => 24.90,
            'subtotal' => 24.90,
        ]);
    }
}
