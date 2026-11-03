<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VentaCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_sale_list_shows_registered_sales(): void
    {
        $cliente = $this->crearCliente();
        $proveedor = $this->crearProveedor();
        $producto = $this->crearProducto($proveedor);
        $venta = $this->crearVenta($cliente);

        $venta->detalleVentas()->create([
            'producto_id' => $producto->id,
            'cantidad' => 2,
            'precio_unitario' => 24.90,
            'subtotal' => 49.80,
        ]);

        $response = $this->get(route('ventas.index'));

        $response->assertOk();
        $response->assertSee('Listado de ventas');
        $response->assertSee($venta->codigo);
        $response->assertSee($cliente->nombres);
        $response->assertSee($cliente->apellidos);
        $response->assertSee('02/11/2026');
        $response->assertSee('S/ 49.80');
        $response->assertSee(route('ventas.show', $venta), false);
    }

    public function test_a_valid_sale_is_registered_and_amounts_are_calculated_on_the_server(): void
    {
        $cliente = $this->crearCliente();
        $proveedor = $this->crearProveedor();
        $arroz = $this->crearProducto($proveedor);
        $leche = $this->crearProducto($proveedor, [
            'nombre' => 'Leche evaporada 400 g',
            'precio' => 4.20,
            'stock' => 60,
        ]);

        $response = $this->post(route('ventas.store'), [
            'cliente_id' => $cliente->id,
            'productos' => [
                [
                    'producto_id' => $arroz->id,
                    'cantidad' => 2,
                    'precio_unitario' => 0.01,
                    'subtotal' => 0.01,
                ],
                [
                    'producto_id' => $leche->id,
                    'cantidad' => 3,
                    'precio_unitario' => 0.01,
                    'subtotal' => 0.01,
                ],
            ],
        ]);

        $venta = Venta::query()->sole();

        $response->assertRedirect(route('ventas.show', $venta));
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Venta registrada correctamente.');
        $this->assertDatabaseHas('ventas', [
            'id' => $venta->id,
            'cliente_id' => $cliente->id,
            'total' => 62.40,
        ]);
        $this->assertSame(now()->toDateString(), $venta->fecha->toDateString());
        $this->assertDatabaseHas('detalle_ventas', [
            'venta_id' => $venta->id,
            'producto_id' => $arroz->id,
            'cantidad' => 2,
            'precio_unitario' => 24.90,
            'subtotal' => 49.80,
        ]);
        $this->assertDatabaseHas('detalle_ventas', [
            'venta_id' => $venta->id,
            'producto_id' => $leche->id,
            'cantidad' => 3,
            'precio_unitario' => 4.20,
            'subtotal' => 12.60,
        ]);
        $this->assertDatabaseCount('detalle_ventas', 2);
        $this->assertSame(38, $arroz->refresh()->stock);
        $this->assertSame(57, $leche->refresh()->stock);
    }

    public function test_a_sale_without_a_client_is_rejected(): void
    {
        $proveedor = $this->crearProveedor();
        $producto = $this->crearProducto($proveedor);

        $response = $this->post(route('ventas.store'), [
            'productos' => [
                ['producto_id' => $producto->id, 'cantidad' => 1],
            ],
        ]);

        $response->assertSessionHasErrors('cliente_id');
        $this->assertDatabaseCount('ventas', 0);
        $this->assertDatabaseCount('detalle_ventas', 0);
    }

    public function test_a_nonexistent_client_is_rejected(): void
    {
        $proveedor = $this->crearProveedor();
        $producto = $this->crearProducto($proveedor);

        $response = $this->post(route('ventas.store'), [
            'cliente_id' => 999999,
            'productos' => [
                ['producto_id' => $producto->id, 'cantidad' => 1],
            ],
        ]);

        $response->assertSessionHasErrors('cliente_id');
        $this->assertDatabaseCount('ventas', 0);
    }

    public function test_a_sale_without_products_is_rejected(): void
    {
        $cliente = $this->crearCliente();

        $response = $this->post(route('ventas.store'), [
            'cliente_id' => $cliente->id,
            'productos' => [],
        ]);

        $response->assertSessionHasErrors('productos');
        $this->assertDatabaseCount('ventas', 0);
        $this->assertDatabaseCount('detalle_ventas', 0);
    }

    public function test_a_quantity_equal_to_zero_is_rejected(): void
    {
        $cliente = $this->crearCliente();
        $proveedor = $this->crearProveedor();
        $producto = $this->crearProducto($proveedor);

        $response = $this->post(route('ventas.store'), $this->datosVenta($cliente, [
            ['producto_id' => $producto->id, 'cantidad' => 0],
        ]));

        $response->assertSessionHasErrors('productos.0.cantidad');
        $this->assertDatabaseCount('ventas', 0);
    }

    public function test_a_negative_quantity_is_rejected(): void
    {
        $cliente = $this->crearCliente();
        $proveedor = $this->crearProveedor();
        $producto = $this->crearProducto($proveedor);

        $response = $this->post(route('ventas.store'), $this->datosVenta($cliente, [
            ['producto_id' => $producto->id, 'cantidad' => -1],
        ]));

        $response->assertSessionHasErrors('productos.0.cantidad');
        $this->assertDatabaseCount('ventas', 0);
    }

    public function test_a_nonexistent_product_is_rejected(): void
    {
        $cliente = $this->crearCliente();

        $response = $this->post(route('ventas.store'), $this->datosVenta($cliente, [
            ['producto_id' => 999999, 'cantidad' => 1],
        ]));

        $response->assertSessionHasErrors('productos.0.producto_id');
        $this->assertDatabaseCount('ventas', 0);
        $this->assertDatabaseCount('detalle_ventas', 0);
    }

    public function test_a_sale_exceeding_the_available_stock_is_rejected(): void
    {
        $cliente = $this->crearCliente();
        $proveedor = $this->crearProveedor();
        $producto = $this->crearProducto($proveedor, ['stock' => 5]);

        $response = $this->post(route('ventas.store'), $this->datosVenta($cliente, [
            ['producto_id' => $producto->id, 'cantidad' => 6],
        ]));

        $response->assertSessionHasErrors('productos');
        $this->assertDatabaseCount('ventas', 0);
        $this->assertDatabaseCount('detalle_ventas', 0);
        $this->assertSame(5, $producto->refresh()->stock);
    }

    public function test_the_sale_detail_shows_products_quantities_and_amounts(): void
    {
        $cliente = $this->crearCliente();
        $proveedor = $this->crearProveedor();
        $producto = $this->crearProducto($proveedor);
        $venta = $this->crearVenta($cliente);

        $venta->detalleVentas()->create([
            'producto_id' => $producto->id,
            'cantidad' => 2,
            'precio_unitario' => 24.90,
            'subtotal' => 49.80,
        ]);

        $response = $this->get(route('ventas.show', $venta));

        $response->assertOk();
        $response->assertSee($venta->codigo);
        $response->assertSee($cliente->nombres);
        $response->assertSee($cliente->apellidos);
        $response->assertSee('02/11/2026');
        $response->assertSee($producto->nombre);
        $response->assertSee($producto->categoria);
        $response->assertSee('S/ 24.90');
        $response->assertSee('S/ 49.80');
        $response->assertSee('2');
    }

    private function crearCliente(array $overrides = []): Cliente
    {
        return Cliente::create(array_merge([
            'nombres' => 'María Elena',
            'apellidos' => 'Quispe Ramos',
            'documento' => '12345678',
            'telefono' => '912345678',
            'email' => 'maria.quispe@example.test',
            'direccion' => 'Jr. Las Flores 456, Lima',
        ], $overrides));
    }

    private function crearProveedor(array $overrides = []): Proveedor
    {
        return Proveedor::create(array_merge([
            'nombre' => 'Distribuidora Los Andes',
            'ruc' => '20123456789',
            'telefono' => '987654321',
            'email' => 'ventas@losandes.test',
            'direccion' => 'Av. Comercial 123, Lima',
        ], $overrides));
    }

    private function crearProducto(Proveedor $proveedor, array $overrides = []): Producto
    {
        return Producto::create(array_merge([
            'nombre' => 'Arroz extra 5 kg',
            'descripcion' => 'Bolsa de arroz ficticia para pruebas',
            'categoria' => 'Abarrotes',
            'precio' => 24.90,
            'stock' => 40,
            'proveedor_id' => $proveedor->id,
        ], $overrides));
    }

    private function crearVenta(Cliente $cliente): Venta
    {
        return Venta::create([
            'cliente_id' => $cliente->id,
            'fecha' => '2026-11-02',
            'total' => 49.80,
        ]);
    }

    private function datosVenta(Cliente $cliente, array $productos): array
    {
        return [
            'cliente_id' => $cliente->id,
            'productos' => $productos,
        ];
    }
}
