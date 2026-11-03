<?php

namespace Tests\Feature;

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\VentaController;
use App\Http\Requests\RegistroVentaRequest;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Venta;
use App\Services\CalculadoraVenta;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class CajaBlancaTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_controller_takes_the_create_branch(): void
    {
        $this->get(route('clientes.create'))->assertOk();
    }

    public function test_provider_controller_takes_the_create_branch(): void
    {
        $this->get(route('proveedores.create'))->assertOk();
    }

    public function test_product_controller_takes_the_create_branch(): void
    {
        $this->get(route('productos.create'))->assertOk();
    }

    public function test_sale_controller_takes_the_create_branch(): void
    {
        $this->get(route('ventas.create'))->assertOk();
    }

    public function test_product_controller_takes_the_blocked_deletion_branch_when_details_exist(): void
    {
        $cliente = $this->crearCliente();
        $proveedor = $this->crearProveedor();
        $producto = $this->crearProducto($proveedor);
        $venta = $this->crearVenta($cliente);
        $venta->detalleVentas()->create([
            'producto_id' => $producto->id,
            'cantidad' => 1,
            'precio_unitario' => 24.90,
            'subtotal' => 24.90,
        ]);

        $response = app(ProductoController::class)->destroy($producto);

        $this->assertSame(route('productos.index'), $response->getTargetUrl());
        $this->assertSame(
            'No se puede eliminar el producto porque está asociado a detalles de venta.',
            session('error')
        );
        $this->assertDatabaseHas('productos', ['id' => $producto->id]);
    }

    public function test_product_controller_takes_the_deletion_branch_when_there_are_no_details(): void
    {
        $producto = $this->crearProducto($this->crearProveedor());

        $response = app(ProductoController::class)->destroy($producto);

        $this->assertSame(route('productos.index'), $response->getTargetUrl());
        $this->assertSame('Producto eliminado correctamente.', session('success'));
        $this->assertDatabaseMissing('productos', ['id' => $producto->id]);
    }

    public function test_provider_controller_takes_the_blocked_deletion_branch_when_products_exist(): void
    {
        $proveedor = $this->crearProveedor();
        $this->crearProducto($proveedor);

        $response = app(ProveedorController::class)->destroy($proveedor);

        $this->assertSame(route('proveedores.index'), $response->getTargetUrl());
        $this->assertSame(
            'No se puede eliminar el proveedor porque tiene productos asociados.',
            session('error')
        );
        $this->assertDatabaseHas('proveedores', ['id' => $proveedor->id]);
    }

    public function test_provider_controller_takes_the_deletion_branch_when_there_are_no_products(): void
    {
        $proveedor = $this->crearProveedor();

        $response = app(ProveedorController::class)->destroy($proveedor);

        $this->assertSame(route('proveedores.index'), $response->getTargetUrl());
        $this->assertSame('Proveedor eliminado correctamente.', session('success'));
        $this->assertDatabaseMissing('proveedores', ['id' => $proveedor->id]);
    }

    public function test_client_controller_takes_the_blocked_deletion_branch_when_sales_exist(): void
    {
        $cliente = $this->crearCliente();
        $this->crearVenta($cliente);

        $response = app(ClienteController::class)->destroy($cliente);

        $this->assertSame(route('clientes.index'), $response->getTargetUrl());
        $this->assertSame(
            'No se puede eliminar el cliente porque tiene ventas asociadas.',
            session('error')
        );
        $this->assertDatabaseHas('clientes', ['id' => $cliente->id]);
    }

    public function test_client_controller_takes_the_deletion_branch_when_there_are_no_sales(): void
    {
        $cliente = $this->crearCliente();

        $response = app(ClienteController::class)->destroy($cliente);

        $this->assertSame(route('clientes.index'), $response->getTargetUrl());
        $this->assertSame('Cliente eliminado correctamente.', session('success'));
        $this->assertDatabaseMissing('clientes', ['id' => $cliente->id]);
    }

    public function test_client_controller_takes_the_database_error_branch(): void
    {
        $cliente = Mockery::mock(Cliente::class);
        $ventas = Mockery::mock(HasMany::class);
        $ventas->shouldReceive('exists')->once()->andReturn(false);
        $cliente->shouldReceive('ventas')->once()->andReturn($ventas);
        $cliente->shouldReceive('delete')->once()->andThrow($this->queryException());

        $response = app(ClienteController::class)->destroy($cliente);

        $this->assertSame(route('clientes.index'), $response->getTargetUrl());
        $this->assertSame(
            'No se puede eliminar el cliente porque tiene registros relacionados.',
            session('error')
        );
    }

    public function test_provider_controller_takes_the_database_error_branch(): void
    {
        $proveedor = Mockery::mock(Proveedor::class);
        $productos = Mockery::mock(HasMany::class);
        $productos->shouldReceive('exists')->once()->andReturn(false);
        $proveedor->shouldReceive('productos')->once()->andReturn($productos);
        $proveedor->shouldReceive('delete')->once()->andThrow($this->queryException());

        $response = app(ProveedorController::class)->destroy($proveedor);

        $this->assertSame(route('proveedores.index'), $response->getTargetUrl());
        $this->assertSame(
            'No se puede eliminar el proveedor porque tiene registros relacionados.',
            session('error')
        );
    }

    public function test_product_controller_takes_the_database_error_branch(): void
    {
        $producto = Mockery::mock(Producto::class);
        $detalles = Mockery::mock(HasMany::class);
        $detalles->shouldReceive('exists')->once()->andReturn(false);
        $producto->shouldReceive('detalleVentas')->once()->andReturn($detalles);
        $producto->shouldReceive('delete')->once()->andThrow($this->queryException());

        $response = app(ProductoController::class)->destroy($producto);

        $this->assertSame(route('productos.index'), $response->getTargetUrl());
        $this->assertSame(
            'No se puede eliminar el producto porque tiene registros relacionados.',
            session('error')
        );
    }

    public function test_sale_controller_takes_the_defensive_missing_product_branch(): void
    {
        $cliente = $this->crearCliente();
        $request = Mockery::mock(RegistroVentaRequest::class);
        $request->shouldReceive('validated')->once()->andReturn([
            'cliente_id' => $cliente->id,
            'productos' => [
                ['producto_id' => 999999, 'cantidad' => 1],
            ],
        ]);

        try {
            app(VentaController::class)->store($request, new CalculadoraVenta);
            $this->fail('Se esperaba una excepción de validación.');
        } catch (ValidationException $exception) {
            $this->assertSame(
                'El producto seleccionado no existe.',
                $exception->errors()['productos.0.producto_id'][0]
            );
        }

        $this->assertDatabaseCount('ventas', 0);
        $this->assertDatabaseCount('detalle_ventas', 0);
    }

    public function test_sale_controller_takes_the_stock_error_branch_and_rolls_back_all_changes(): void
    {
        $cliente = $this->crearCliente();
        $producto = $this->crearProducto($this->crearProveedor(), ['stock' => 2]);

        $response = $this->post(route('ventas.store'), [
            'cliente_id' => $cliente->id,
            'productos' => [
                ['producto_id' => $producto->id, 'cantidad' => 3],
            ],
        ]);

        $response->assertSessionHasErrors('productos');
        $this->assertDatabaseCount('ventas', 0);
        $this->assertDatabaseCount('detalle_ventas', 0);
        $this->assertSame(2, $producto->refresh()->stock);
    }

    public function test_sale_controller_takes_the_success_branch_and_persists_all_changes(): void
    {
        $cliente = $this->crearCliente();
        $producto = $this->crearProducto($this->crearProveedor(), ['stock' => 10]);

        $response = $this->post(route('ventas.store'), [
            'cliente_id' => $cliente->id,
            'productos' => [
                ['producto_id' => $producto->id, 'cantidad' => 2],
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
        $this->assertDatabaseCount('ventas', 1);

        $venta = Venta::query()->sole();

        $response->assertRedirect(route('ventas.show', $venta));
        $this->assertDatabaseHas('ventas', [
            'id' => $venta->id,
            'cliente_id' => $cliente->id,
            'total' => 49.80,
        ]);
        $this->assertDatabaseHas('detalle_ventas', [
            'venta_id' => $venta->id,
            'producto_id' => $producto->id,
            'cantidad' => 2,
            'precio_unitario' => 24.90,
            'subtotal' => 49.80,
        ]);
        $this->assertSame(8, $producto->refresh()->stock);
    }

    private function queryException(): QueryException
    {
        return new QueryException('sqlite', 'delete from registros', [], new \RuntimeException('Fallo simulado.'));
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
            'total' => 24.90,
        ]);
    }
}
