<?php

namespace Tests\Unit;

use App\Http\Requests\ClienteRequest;
use App\Http\Requests\ProductoRequest;
use App\Http\Requests\ProveedorRequest;
use App\Http\Requests\RegistroVentaRequest;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\Validator;
use Tests\TestCase;

class ValidacionReglasTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_request_accepts_valid_data(): void
    {
        $validator = $this->validar(new ClienteRequest, $this->datosCliente());

        $this->assertFalse($validator->fails());
    }

    public function test_client_request_rejects_missing_required_fields(): void
    {
        $validator = $this->validar(new ClienteRequest, [
            'nombres' => '',
            'apellidos' => '',
            'documento' => '',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nombres', $validator->errors()->messages());
        $this->assertArrayHasKey('apellidos', $validator->errors()->messages());
        $this->assertArrayHasKey('documento', $validator->errors()->messages());
    }

    public function test_client_request_rejects_invalid_email_and_phone(): void
    {
        $validator = $this->validar(new ClienteRequest, $this->datosCliente([
            'email' => 'correo-invalido',
            'telefono' => '123',
        ]));

        $this->assertTrue($validator->fails());
        $this->assertSame('Ingresa un correo electrónico válido.', $validator->errors()->first('email'));
        $this->assertSame('El teléfono debe tener al menos 6 caracteres.', $validator->errors()->first('telefono'));
    }

    public function test_provider_request_accepts_valid_data(): void
    {
        $validator = $this->validar(new ProveedorRequest, $this->datosProveedor());

        $this->assertFalse($validator->fails());
    }

    public function test_provider_request_rejects_duplicate_ruc(): void
    {
        $proveedor = $this->crearProveedor();

        $validator = $this->validar(new ProveedorRequest, $this->datosProveedor([
            'ruc' => $proveedor->ruc,
            'email' => 'otro@example.test',
        ]));

        $this->assertTrue($validator->fails());
        $this->assertSame('Ya existe un proveedor registrado con este RUC.', $validator->errors()->first('ruc'));
    }

    public function test_product_request_accepts_valid_data(): void
    {
        $proveedor = $this->crearProveedor();

        $validator = $this->validar(new ProductoRequest, $this->datosProducto($proveedor));

        $this->assertFalse($validator->fails());
    }

    public function test_product_request_rejects_price_equal_to_zero(): void
    {
        $proveedor = $this->crearProveedor();

        $validator = $this->validar(new ProductoRequest, $this->datosProducto($proveedor, [
            'precio' => 0,
        ]));

        $this->assertTrue($validator->fails());
        $this->assertSame('El precio debe ser mayor que cero.', $validator->errors()->first('precio'));
    }

    public function test_product_request_rejects_negative_price(): void
    {
        $proveedor = $this->crearProveedor();

        $validator = $this->validar(new ProductoRequest, $this->datosProducto($proveedor, [
            'precio' => -1,
        ]));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('precio', $validator->errors()->messages());
    }

    public function test_product_request_rejects_negative_stock(): void
    {
        $proveedor = $this->crearProveedor();

        $validator = $this->validar(new ProductoRequest, $this->datosProducto($proveedor, [
            'stock' => -1,
        ]));

        $this->assertTrue($validator->fails());
        $this->assertSame('El stock no puede ser negativo.', $validator->errors()->first('stock'));
    }

    public function test_product_request_rejects_a_nonexistent_provider(): void
    {
        $validator = $this->validar(new ProductoRequest, [
            'nombre' => 'Arroz extra 5 kg',
            'descripcion' => null,
            'categoria' => 'Abarrotes',
            'precio' => 24.90,
            'stock' => 40,
            'proveedor_id' => 999999,
        ]);

        $this->assertTrue($validator->fails());
        $this->assertSame('El proveedor seleccionado no existe.', $validator->errors()->first('proveedor_id'));
    }

    public function test_sale_request_accepts_valid_data(): void
    {
        $cliente = $this->crearCliente();
        $producto = $this->crearProducto($this->crearProveedor());

        $validator = $this->validar(new RegistroVentaRequest, [
            'cliente_id' => $cliente->id,
            'productos' => [
                ['producto_id' => $producto->id, 'cantidad' => 2],
            ],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function test_sale_request_rejects_a_missing_client(): void
    {
        $producto = $this->crearProducto($this->crearProveedor());

        $validator = $this->validar(new RegistroVentaRequest, [
            'productos' => [
                ['producto_id' => $producto->id, 'cantidad' => 1],
            ],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertSame('Debes seleccionar un cliente.', $validator->errors()->first('cliente_id'));
    }

    public function test_sale_request_rejects_an_empty_product_list(): void
    {
        $cliente = $this->crearCliente();

        $validator = $this->validar(new RegistroVentaRequest, [
            'cliente_id' => $cliente->id,
            'productos' => [],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('productos', $validator->errors()->messages());
    }

    public function test_sale_request_rejects_quantity_equal_to_zero(): void
    {
        $cliente = $this->crearCliente();
        $producto = $this->crearProducto($this->crearProveedor());

        $validator = $this->validar(new RegistroVentaRequest, [
            'cliente_id' => $cliente->id,
            'productos' => [
                ['producto_id' => $producto->id, 'cantidad' => 0],
            ],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertSame('La cantidad debe ser mayor que cero.', $validator->errors()->first('productos.0.cantidad'));
    }

    public function test_sale_request_rejects_a_nonexistent_product(): void
    {
        $cliente = $this->crearCliente();

        $validator = $this->validar(new RegistroVentaRequest, [
            'cliente_id' => $cliente->id,
            'productos' => [
                ['producto_id' => 999999, 'cantidad' => 1],
            ],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertSame('El producto seleccionado no existe.', $validator->errors()->first('productos.0.producto_id'));
    }

    public function test_sale_request_rejects_a_repeated_product(): void
    {
        $cliente = $this->crearCliente();
        $producto = $this->crearProducto($this->crearProveedor());

        $validator = $this->validar(new RegistroVentaRequest, [
            'cliente_id' => $cliente->id,
            'productos' => [
                ['producto_id' => $producto->id, 'cantidad' => 1],
                ['producto_id' => $producto->id, 'cantidad' => 2],
            ],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertSame('No puedes repetir el mismo producto en la venta.', $validator->errors()->first('productos.1.producto_id'));
    }

    private function validar(FormRequest $request, array $datos): Validator
    {
        return validator($datos, $request->rules(), $request->messages(), $request->attributes());
    }

    private function datosCliente(array $overrides = []): array
    {
        return array_merge([
            'nombres' => 'Lucía',
            'apellidos' => 'Fernández Rojas',
            'documento' => '87654321',
            'telefono' => '987654321',
            'email' => 'lucia.fernandez@example.test',
            'direccion' => 'Av. Los Álamos 123, Lima',
        ], $overrides);
    }

    private function datosProveedor(array $overrides = []): array
    {
        return array_merge([
            'nombre' => 'Distribuidora Los Andes',
            'ruc' => '20123456789',
            'telefono' => '987654321',
            'email' => 'ventas@losandes.test',
            'direccion' => 'Av. Comercial 123, Lima',
        ], $overrides);
    }

    private function datosProducto(Proveedor $proveedor, array $overrides = []): array
    {
        return array_merge([
            'nombre' => 'Arroz extra 5 kg',
            'descripcion' => 'Bolsa de arroz ficticia para pruebas',
            'categoria' => 'Abarrotes',
            'precio' => 24.90,
            'stock' => 40,
            'proveedor_id' => $proveedor->id,
        ], $overrides);
    }

    private function crearCliente(): Cliente
    {
        return Cliente::create($this->datosCliente());
    }

    private function crearProveedor(): Proveedor
    {
        return Proveedor::create($this->datosProveedor());
    }

    private function crearProducto(Proveedor $proveedor): Producto
    {
        return Producto::create($this->datosProducto($proveedor));
    }
}
