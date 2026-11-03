<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccesibilidadTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_pages_include_the_accessibility_panel_and_skip_link(): void
    {
        $rutas = [
            route('home'),
            route('clientes.index'),
            route('clientes.create'),
            route('proveedores.index'),
            route('proveedores.create'),
            route('productos.index'),
            route('productos.create'),
            route('ventas.index'),
            route('ventas.create'),
        ];

        foreach ($rutas as $ruta) {
            $response = $this->get($ruta);

            $response->assertOk();
            $response->assertSee('Saltar al contenido principal');
            $response->assertSee('href="#contenido"', false);
            $response->assertSee('id="contenido" tabindex="-1"', false);
            $response->assertSee('data-a11y-toggle', false);
            $response->assertSee('id="panel-accesibilidad"', false);
            $response->assertSee('aria-haspopup="dialog"', false);
            $response->assertSee('data-a11y-text-increase', false);
            $response->assertSee('data-a11y-text-decrease', false);
            $response->assertSee('data-a11y-contrast-toggle', false);
            $response->assertSee('data-a11y-reset', false);
            $response->assertSee('Uso con teclado');
            $response->assertSee('Modo oscuro de alto contraste');
            $response->assertSee('data-a11y-status', false);
            $response->assertSee('lang="es"', false);
        }
    }

    public function test_text_size_and_contrast_preferences_are_restored_before_rendering(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('localStorage.getItem("tienda-a11y-text")', false);
        $response->assertSee('localStorage.getItem("tienda-a11y-contrast")', false);
        $response->assertSee('data-a11y-text', false);
        $response->assertSee('data-a11y-contrast', false);
    }

    public function test_forms_have_labels_required_indicators_and_help_text(): void
    {
        $response = $this->get(route('clientes.create'));

        $response->assertOk();
        $response->assertSee('for="nombres"', false);
        $response->assertSee('for="apellidos"', false);
        $response->assertSee('for="documento"', false);
        $response->assertSee('id="documento-help"', false);
        $response->assertSee('aria-describedby="documento-help', false);
        $response->assertSee('(obligatorio)');
        $response->assertSee('asterisco');
        $response->assertSee('required', false);

        $proveedores = $this->get(route('proveedores.create'));
        $proveedores->assertSee('for="ruc"', false);
        $proveedores->assertSee('id="ruc-help"', false);
        $proveedores->assertSee('(obligatorio)');

        $productos = $this->get(route('productos.create'));
        $productos->assertSee('for="categoria"', false);
        $productos->assertSee('for="proveedor_id"', false);
        $productos->assertSee('id="precio-help"', false);
        $productos->assertSee('id="stock-help"', false);
        $productos->assertSee('(obligatorio)');
    }

    public function test_validation_errors_are_visible_and_described_next_to_the_field(): void
    {
        $response = $this->from(route('clientes.create'))->followingRedirects()->post(route('clientes.store'), []);

        $response->assertOk();
        $response->assertSee('No se pudo guardar la información.');
        $response->assertSee('role="alert"', false);
        $response->assertSee('id="nombres-error"', false);
        $response->assertSee('aria-invalid="true"', false);
        $response->assertSee('Los nombres son obligatorios.');
    }

    public function test_flash_messages_are_exposed_as_status_and_alert_regions(): void
    {
        $this->post(route('clientes.store'), $this->datosCliente())->assertRedirect(route('clientes.index'));

        $exito = $this->get(route('clientes.index'));
        $exito->assertSee('Cliente registrado correctamente.');
        $exito->assertSee('class="alert alert--success" role="status"', false);

        $cliente = Cliente::query()->firstOrFail();
        $this->crearVenta($cliente);

        $this->delete(route('clientes.destroy', $cliente))->assertRedirect(route('clientes.index'));

        $error = $this->get(route('clientes.index'));
        $error->assertSee('No se puede eliminar el cliente porque tiene ventas asociadas.');
        $error->assertSee('class="alert alert--error" role="alert"', false);
        $error->assertSee('data-confirm="¿Eliminar al cliente', false);
    }

    public function test_tables_and_navigation_expose_accessible_names(): void
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

        $clientes = $this->get(route('clientes.index'));
        $clientes->assertSee('aria-label="Navegación principal"', false);
        $clientes->assertSee('aria-label="Ruta de navegación"', false);
        $clientes->assertSee('aria-current="page"', false);
        $clientes->assertSee('<caption class="sr-only">Listado de clientes registrados</caption>', false);
        $clientes->assertSee('scope="col"', false);
        $clientes->assertSee('aria-label="Editar cliente ', false);
        $clientes->assertSee('aria-label="Eliminar cliente ', false);

        $proveedores = $this->get(route('proveedores.index'));
        $proveedores->assertSee('<caption class="sr-only">Listado de proveedores registrados</caption>', false);
        $proveedores->assertSee('aria-label="Editar proveedor ', false);

        $productos = $this->get(route('productos.index'));
        $productos->assertSee('<caption class="sr-only">Listado de productos del catálogo</caption>', false);
        $productos->assertSee('aria-label="Editar producto ', false);

        $ventas = $this->get(route('ventas.index'));
        $ventas->assertSee('<caption class="sr-only">Listado de ventas registradas</caption>', false);
        $ventas->assertSee('aria-label="Ver detalle de la venta ', false);

        $detalle = $this->get(route('ventas.show', $venta));
        $detalle->assertSee('aria-label="Datos generales de la venta"', false);
        $detalle->assertSee('aria-label="Productos de la venta"', false);
        $detalle->assertSee('<caption class="sr-only">Productos vendidos en la venta ', false);
        $detalle->assertSee('scope="row"', false);
    }

    public function test_each_page_has_a_single_main_heading(): void
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

        $rutas = [
            route('home'),
            route('clientes.index'),
            route('clientes.create'),
            route('clientes.edit', $cliente),
            route('proveedores.index'),
            route('proveedores.create'),
            route('proveedores.edit', $proveedor),
            route('productos.index'),
            route('productos.create'),
            route('productos.edit', $producto),
            route('ventas.index'),
            route('ventas.create'),
            route('ventas.show', $venta),
        ];

        foreach ($rutas as $ruta) {
            $response = $this->get($ruta);

            $response->assertOk();
            $this->assertSame(1, substr_count($response->getContent(), '<h1'), 'La página '.$ruta.' debe tener un solo h1.');
        }
    }

    public function test_the_sale_form_announces_totals_and_adds_rows_with_keyboard_support(): void
    {
        $cliente = $this->crearCliente();
        $proveedor = $this->crearProveedor();
        $this->crearProducto($proveedor);

        $response = $this->get(route('ventas.create'));

        $response->assertOk();
        $response->assertSee('for="cliente_id"', false);
        $response->assertSee('aria-labelledby="productos-venta-title"', false);
        $response->assertSee('aria-describedby="productos-venta-help"', false);
        $response->assertSee('data-sale-announcer', false);
        $response->assertSee('aria-labelledby="sale-total-label"', false);
        $response->assertSee('aria-live="polite"', false);
        $response->assertSee('data-add-line', false);
        $response->assertSee('data-remove-line', false);
        $response->assertSee('aria-label="Quitar producto de la venta"', false);
    }

    public function test_the_accessibility_styles_keep_focus_and_touch_targets_visible(): void
    {
        $css = file_get_contents(public_path('css/app.css'));

        $this->assertStringContainsString(':focus-visible', $css);
        $this->assertStringContainsString('outline: 3px solid var(--focus)', $css);
        $this->assertStringContainsString('html[data-a11y-contrast="high"]', $css);
        $this->assertStringContainsString('--background: #0b1411', $css);
        $this->assertStringContainsString('color-scheme: dark', $css);
        $this->assertStringContainsString('min-height: 2.75rem', $css);
        $this->assertStringContainsString('prefers-reduced-motion', $css);
    }

    private function datosCliente(array $overrides = []): array
    {
        return array_merge([
            'nombres' => 'María Elena',
            'apellidos' => 'Quispe Ramos',
            'documento' => '12345678',
            'telefono' => '912345678',
            'email' => 'maria.quispe@example.test',
            'direccion' => 'Jr. Las Flores 456, Lima',
        ], $overrides);
    }

    private function crearCliente(array $overrides = []): Cliente
    {
        return Cliente::create($this->datosCliente($overrides));
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
}
