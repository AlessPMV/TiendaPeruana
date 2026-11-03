<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_client_list_shows_registered_clients(): void
    {
        $cliente = Cliente::create($this->datosCliente());

        $response = $this->get(route('clientes.index'));

        $response->assertOk();
        $response->assertSee('Listado de clientes');
        $response->assertSee($cliente->nombres);
        $response->assertSee($cliente->apellidos);
        $response->assertSee($cliente->documento);
        $response->assertSee($cliente->email);
    }

    public function test_a_valid_client_can_be_registered(): void
    {
        $datos = $this->datosCliente();

        $response = $this->post(route('clientes.store'), $datos);

        $response->assertRedirect(route('clientes.index'));
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Cliente registrado correctamente.');
        $this->assertDatabaseHas('clientes', [
            'documento' => $datos['documento'],
            'email' => $datos['email'],
            'telefono' => $datos['telefono'],
        ]);
    }

    public function test_a_client_can_be_registered_without_optional_contact_data(): void
    {
        $datos = $this->datosCliente([
            'documento' => '44556677',
            'email' => null,
            'telefono' => null,
            'direccion' => null,
        ]);

        $response = $this->post(route('clientes.store'), $datos);

        $response->assertRedirect(route('clientes.index'));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('clientes', [
            'documento' => '44556677',
            'email' => null,
            'telefono' => null,
        ]);
    }

    public function test_required_fields_are_validated_when_registering(): void
    {
        $response = $this->post(route('clientes.store'), [
            'nombres' => '',
            'apellidos' => '',
            'documento' => '',
            'telefono' => null,
            'email' => null,
            'direccion' => null,
        ]);

        $response->assertSessionHasErrors(['nombres', 'apellidos', 'documento']);
        $response->assertSessionDoesntHaveErrors(['telefono', 'email', 'direccion']);
        $this->assertDatabaseCount('clientes', 0);
    }

    public function test_duplicate_documents_are_rejected(): void
    {
        Cliente::create($this->datosCliente());

        $response = $this->post(route('clientes.store'), $this->datosCliente([
            'nombres' => 'Otro',
            'apellidos' => 'Cliente',
            'email' => 'otro.cliente@example.test',
        ]));

        $response->assertSessionHasErrors('documento');
        $this->assertDatabaseCount('clientes', 1);
    }

    public function test_email_format_and_phone_length_are_validated(): void
    {
        $response = $this->post(route('clientes.store'), $this->datosCliente([
            'email' => 'correo-invalido',
            'telefono' => '123',
        ]));

        $response->assertSessionHasErrors(['email', 'telefono']);
        $this->assertDatabaseCount('clientes', 0);
    }

    public function test_the_edit_form_shows_the_client_data(): void
    {
        $cliente = Cliente::create($this->datosCliente());

        $response = $this->get(route('clientes.edit', $cliente));

        $response->assertOk();
        $response->assertSee('Editar cliente');
        $response->assertSee($cliente->nombres);
        $response->assertSee($cliente->apellidos);
        $response->assertSee($cliente->documento);
        $response->assertSee($cliente->email);
        $response->assertSee(route('clientes.update', $cliente), false);
    }

    public function test_a_client_can_be_updated(): void
    {
        $cliente = Cliente::create($this->datosCliente());

        $response = $this->put(route('clientes.update', $cliente), $this->datosCliente([
            'nombres' => 'Lucía Beatriz',
            'apellidos' => 'Fernández Torres',
            'telefono' => '999 888 777',
            'direccion' => 'Jr. Nueva Dirección 789, Lima',
        ]));

        $response->assertRedirect(route('clientes.index'));
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Cliente actualizado correctamente.');
        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'nombres' => 'Lucía Beatriz',
            'apellidos' => 'Fernández Torres',
            'documento' => $cliente->documento,
            'telefono' => '999 888 777',
        ]);
    }

    public function test_a_client_without_sales_can_be_deleted(): void
    {
        $cliente = Cliente::create($this->datosCliente());

        $response = $this->delete(route('clientes.destroy', $cliente));

        $response->assertRedirect(route('clientes.index'));
        $response->assertSessionHas('success', 'Cliente eliminado correctamente.');
        $this->assertDatabaseMissing('clientes', ['id' => $cliente->id]);
    }

    public function test_a_client_with_sales_cannot_be_deleted(): void
    {
        $cliente = Cliente::create($this->datosCliente());

        $venta = Venta::create([
            'cliente_id' => $cliente->id,
            'fecha' => '2026-11-02',
            'total' => 0,
        ]);

        $response = $this->delete(route('clientes.destroy', $cliente));

        $response->assertRedirect(route('clientes.index'));
        $response->assertSessionHas('error', 'No se puede eliminar el cliente porque tiene ventas asociadas.');
        $this->assertDatabaseHas('clientes', ['id' => $cliente->id]);
        $this->assertDatabaseHas('ventas', ['id' => $venta->id]);
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
}
