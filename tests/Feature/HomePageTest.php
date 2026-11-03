<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewIs('home');
    }

    public function test_home_page_shows_the_application_identity(): void
    {
        $response = $this->get('/');

        $response->assertSee('Sistema de Gestión de Ventas');
        $response->assertSee('Proyecto académico');
        $response->assertSee('Tienda peruana');
    }

    public function test_home_page_shows_the_initial_navigation(): void
    {
        $response = $this->get(route('home'));

        $response->assertSee('Inicio');
        $response->assertSee('Clientes');
        $response->assertSee('Proveedores');
        $response->assertSee('Productos');
        $response->assertSee('Ventas');
        $response->assertSee('aria-label="Navegación principal"', false);
        $response->assertSee('data-nav-toggle', false);
    }

    public function test_home_page_shows_the_prepared_modules_and_categories(): void
    {
        $response = $this->get('/');

        $response->assertSee('Una base lista para crecer');
        $response->assertSee('Categorías de ejemplo');
        $response->assertSee('Abarrotes');
        $response->assertSee('Bebidas');
        $response->assertSee('Lácteos');
        $response->assertSee('Productos congelados');
    }
}
