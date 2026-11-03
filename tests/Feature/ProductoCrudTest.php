<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_product_list_shows_registered_products(): void
    {
        $proveedor = $this->crearProveedor();
        $producto = Producto::create($this->datosProducto($proveedor));

        $response = $this->get(route('productos.index'));

        $response->assertOk();
        $response->assertSee('Listado de productos');
        $response->assertSee($producto->nombre);
        $response->assertSee($producto->descripcion);
        $response->assertSee($producto->categoria);
        $response->assertSee('S/ 24.90');
        $response->assertSee($proveedor->nombre);
    }

    public function test_a_valid_product_can_be_registered(): void
    {
        $proveedor = $this->crearProveedor();
        $datos = $this->datosProducto($proveedor);

        $response = $this->post(route('productos.store'), $datos);

        $response->assertRedirect(route('productos.index'));
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Producto registrado correctamente.');
        $this->assertDatabaseHas('productos', [
            'nombre' => $datos['nombre'],
            'categoria' => $datos['categoria'],
            'precio' => $datos['precio'],
            'stock' => $datos['stock'],
            'proveedor_id' => $proveedor->id,
        ]);
    }

    public function test_a_product_can_be_registered_without_a_description(): void
    {
        $proveedor = $this->crearProveedor();
        $datos = $this->datosProducto($proveedor, [
            'nombre' => 'Agua mineral 625 ml',
            'descripcion' => null,
        ]);

        $response = $this->post(route('productos.store'), $datos);

        $response->assertRedirect(route('productos.index'));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('productos', [
            'nombre' => 'Agua mineral 625 ml',
            'descripcion' => null,
        ]);
    }

    public function test_required_fields_are_validated_when_registering(): void
    {
        $response = $this->post(route('productos.store'), [
            'nombre' => '',
            'descripcion' => null,
            'categoria' => '',
            'precio' => '',
            'stock' => '',
            'proveedor_id' => '',
        ]);

        $response->assertSessionHasErrors(['nombre', 'categoria', 'precio', 'stock', 'proveedor_id']);
        $response->assertSessionDoesntHaveErrors('descripcion');
        $this->assertDatabaseCount('productos', 0);
    }

    public function test_a_negative_price_is_rejected(): void
    {
        $proveedor = $this->crearProveedor();

        $response = $this->post(route('productos.store'), $this->datosProducto($proveedor, [
            'precio' => -5,
        ]));

        $response->assertSessionHasErrors('precio');
        $this->assertDatabaseCount('productos', 0);
    }

    public function test_a_price_equal_to_zero_is_rejected(): void
    {
        $proveedor = $this->crearProveedor();

        $response = $this->post(route('productos.store'), $this->datosProducto($proveedor, [
            'precio' => 0,
        ]));

        $response->assertSessionHasErrors('precio');
        $this->assertDatabaseCount('productos', 0);
    }

    public function test_negative_stock_is_rejected(): void
    {
        $proveedor = $this->crearProveedor();

        $response = $this->post(route('productos.store'), $this->datosProducto($proveedor, [
            'stock' => -1,
        ]));

        $response->assertSessionHasErrors('stock');
        $this->assertDatabaseCount('productos', 0);
    }

    public function test_a_nonexistent_provider_is_rejected(): void
    {
        $response = $this->post(route('productos.store'), [
            'nombre' => 'Arroz extra 5 kg',
            'descripcion' => 'Producto ficticio para pruebas',
            'categoria' => 'Abarrotes',
            'precio' => 24.90,
            'stock' => 40,
            'proveedor_id' => 999999,
        ]);

        $response->assertSessionHasErrors('proveedor_id');
        $this->assertDatabaseCount('productos', 0);
    }

    public function test_the_edit_form_shows_the_product_data(): void
    {
        $proveedor = $this->crearProveedor();
        $producto = Producto::create($this->datosProducto($proveedor));

        $response = $this->get(route('productos.edit', $producto));

        $response->assertOk();
        $response->assertSee('Editar producto');
        $response->assertSee($producto->nombre);
        $response->assertSee($producto->descripcion);
        $response->assertSee($producto->categoria);
        $response->assertSee($proveedor->nombre);
        $response->assertSee(route('productos.update', $producto), false);
    }

    public function test_a_product_can_be_updated(): void
    {
        $proveedor = $this->crearProveedor();
        $nuevoProveedor = $this->crearProveedor([
            'nombre' => 'Alimentos del Pacífico S.A.C.',
            'ruc' => '20987654321',
            'email' => 'contacto@pacifico.test',
        ]);
        $producto = Producto::create($this->datosProducto($proveedor));

        $response = $this->put(route('productos.update', $producto), $this->datosProducto($nuevoProveedor, [
            'nombre' => 'Arroz extra 5 kg actualizado',
            'categoria' => 'Abarrotes',
            'precio' => 26.50,
            'stock' => 55,
        ]));

        $response->assertRedirect(route('productos.index'));
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Producto actualizado correctamente.');
        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'nombre' => 'Arroz extra 5 kg actualizado',
            'precio' => 26.50,
            'stock' => 55,
            'proveedor_id' => $nuevoProveedor->id,
        ]);
    }

    public function test_a_product_without_sales_can_be_deleted(): void
    {
        $proveedor = $this->crearProveedor();
        $producto = Producto::create($this->datosProducto($proveedor));

        $response = $this->delete(route('productos.destroy', $producto));

        $response->assertRedirect(route('productos.index'));
        $response->assertSessionHas('success', 'Producto eliminado correctamente.');
        $this->assertDatabaseMissing('productos', ['id' => $producto->id]);
    }

    public function test_a_product_used_in_a_sale_cannot_be_deleted(): void
    {
        $proveedor = $this->crearProveedor();
        $producto = Producto::create($this->datosProducto($proveedor));
        $cliente = Cliente::create([
            'nombres' => 'María Elena',
            'apellidos' => 'Quispe Ramos',
            'documento' => '12345678',
            'telefono' => '912345678',
            'email' => 'maria.quispe@example.test',
            'direccion' => 'Jr. Las Flores 456, Lima',
        ]);
        $venta = Venta::create([
            'cliente_id' => $cliente->id,
            'fecha' => '2026-11-02',
            'total' => 24.90,
        ]);
        $detalle = DetalleVenta::create([
            'venta_id' => $venta->id,
            'producto_id' => $producto->id,
            'cantidad' => 1,
            'precio_unitario' => 24.90,
            'subtotal' => 24.90,
        ]);

        $response = $this->delete(route('productos.destroy', $producto));

        $response->assertRedirect(route('productos.index'));
        $response->assertSessionHas('error', 'No se puede eliminar el producto porque está asociado a detalles de venta.');
        $this->assertDatabaseHas('productos', ['id' => $producto->id]);
        $this->assertDatabaseHas('detalle_ventas', ['id' => $detalle->id]);
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

    private function datosProducto(Proveedor $proveedor, array $overrides = []): array
    {
        return array_merge([
            'nombre' => 'Arroz extra 5 kg',
            'descripcion' => 'Bolsa de arroz extra de grano largo para pruebas.',
            'categoria' => 'Abarrotes',
            'precio' => 24.90,
            'stock' => 40,
            'proveedor_id' => $proveedor->id,
        ], $overrides);
    }
}
