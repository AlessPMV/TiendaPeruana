<?php

namespace Tests\Feature;

use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProveedorCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_provider_list_shows_registered_providers(): void
    {
        $proveedor = Proveedor::create($this->datosProveedor());

        $response = $this->get(route('proveedores.index'));

        $response->assertOk();
        $response->assertSee('Listado de proveedores');
        $response->assertSee($proveedor->nombre);
        $response->assertSee($proveedor->ruc);
        $response->assertSee($proveedor->email);
        $response->assertSee($proveedor->direccion);
    }

    public function test_a_valid_provider_can_be_registered(): void
    {
        $datos = $this->datosProveedor();

        $response = $this->post(route('proveedores.store'), $datos);

        $response->assertRedirect(route('proveedores.index'));
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Proveedor registrado correctamente.');
        $this->assertDatabaseHas('proveedores', [
            'ruc' => $datos['ruc'],
            'email' => $datos['email'],
            'telefono' => $datos['telefono'],
        ]);
    }

    public function test_a_provider_can_be_registered_without_optional_contact_data(): void
    {
        $datos = $this->datosProveedor([
            'ruc' => '20999999999',
            'email' => null,
            'telefono' => null,
            'direccion' => null,
        ]);

        $response = $this->post(route('proveedores.store'), $datos);

        $response->assertRedirect(route('proveedores.index'));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('proveedores', [
            'ruc' => '20999999999',
            'email' => null,
            'telefono' => null,
        ]);
    }

    public function test_required_fields_are_validated_when_registering(): void
    {
        $response = $this->post(route('proveedores.store'), [
            'nombre' => '',
            'ruc' => '',
            'telefono' => null,
            'email' => null,
            'direccion' => null,
        ]);

        $response->assertSessionHasErrors(['nombre', 'ruc']);
        $response->assertSessionDoesntHaveErrors(['telefono', 'email', 'direccion']);
        $this->assertDatabaseCount('proveedores', 0);
    }

    public function test_duplicate_ruc_is_rejected(): void
    {
        Proveedor::create($this->datosProveedor());

        $response = $this->post(route('proveedores.store'), $this->datosProveedor([
            'nombre' => 'Otro Proveedor',
            'email' => 'otro.proveedor@example.test',
        ]));

        $response->assertSessionHasErrors('ruc');
        $this->assertDatabaseCount('proveedores', 1);
    }

    public function test_email_format_and_phone_length_are_validated(): void
    {
        $response = $this->post(route('proveedores.store'), $this->datosProveedor([
            'email' => 'correo-invalido',
            'telefono' => '123',
        ]));

        $response->assertSessionHasErrors(['email', 'telefono']);
        $this->assertDatabaseCount('proveedores', 0);
    }

    public function test_the_edit_form_shows_the_provider_data(): void
    {
        $proveedor = Proveedor::create($this->datosProveedor());

        $response = $this->get(route('proveedores.edit', $proveedor));

        $response->assertOk();
        $response->assertSee('Editar proveedor');
        $response->assertSee($proveedor->nombre);
        $response->assertSee($proveedor->ruc);
        $response->assertSee($proveedor->email);
        $response->assertSee($proveedor->direccion);
        $response->assertSee(route('proveedores.update', $proveedor), false);
    }

    public function test_a_provider_can_be_updated(): void
    {
        $proveedor = Proveedor::create($this->datosProveedor());

        $response = $this->put(route('proveedores.update', $proveedor), $this->datosProveedor([
            'nombre' => 'Distribuidora Los Andes del Sur',
            'telefono' => '999 888 777',
            'direccion' => 'Av. Nueva Dirección 789, Lima',
        ]));

        $response->assertRedirect(route('proveedores.index'));
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Proveedor actualizado correctamente.');
        $this->assertDatabaseHas('proveedores', [
            'id' => $proveedor->id,
            'nombre' => 'Distribuidora Los Andes del Sur',
            'ruc' => $proveedor->ruc,
            'telefono' => '999 888 777',
        ]);
    }

    public function test_a_provider_without_products_can_be_deleted(): void
    {
        $proveedor = Proveedor::create($this->datosProveedor());

        $response = $this->delete(route('proveedores.destroy', $proveedor));

        $response->assertRedirect(route('proveedores.index'));
        $response->assertSessionHas('success', 'Proveedor eliminado correctamente.');
        $this->assertDatabaseMissing('proveedores', ['id' => $proveedor->id]);
    }

    public function test_a_provider_with_products_cannot_be_deleted(): void
    {
        $proveedor = Proveedor::create($this->datosProveedor());

        $producto = Producto::create([
            'nombre' => 'Arroz extra 5 kg',
            'descripcion' => 'Producto ficticio para pruebas',
            'categoria' => 'Abarrotes',
            'precio' => 24.90,
            'stock' => 40,
            'proveedor_id' => $proveedor->id,
        ]);

        $response = $this->delete(route('proveedores.destroy', $proveedor));

        $response->assertRedirect(route('proveedores.index'));
        $response->assertSessionHas('error', 'No se puede eliminar el proveedor porque tiene productos asociados.');
        $this->assertDatabaseHas('proveedores', ['id' => $proveedor->id]);
        $this->assertDatabaseHas('productos', ['id' => $producto->id]);
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
}
