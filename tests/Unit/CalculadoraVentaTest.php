<?php

namespace Tests\Unit;

use App\Services\CalculadoraVenta;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CalculadoraVentaTest extends TestCase
{
    public function test_normaliza_un_precio_a_dos_decimales(): void
    {
        $calculadora = new CalculadoraVenta;

        $this->assertSame(24.90, $calculadora->normalizarPrecio('24.90'));
    }

    public function test_calcula_el_subtotal_de_un_producto(): void
    {
        $calculadora = new CalculadoraVenta;

        $this->assertEqualsWithDelta(49.80, $calculadora->calcularSubtotal(24.90, 2), 0.0001);
    }

    public function test_calcula_el_subtotal_con_precio_decimal_y_varias_unidades(): void
    {
        $calculadora = new CalculadoraVenta;

        $this->assertEqualsWithDelta(12.60, $calculadora->calcularSubtotal('4.20', 3), 0.0001);
    }

    public function test_calcula_el_total_de_varios_subtotales(): void
    {
        $calculadora = new CalculadoraVenta;

        $this->assertEqualsWithDelta(62.40, $calculadora->calcularTotal([49.80, 12.60]), 0.0001);
    }

    public function test_calcula_el_total_sin_errores_de_redondeo(): void
    {
        $calculadora = new CalculadoraVenta;

        $this->assertEqualsWithDelta(0.30, $calculadora->calcularTotal([0.10, 0.10, 0.10]), 0.0001);
    }

    public function test_un_total_sin_subtotales_es_cero(): void
    {
        $calculadora = new CalculadoraVenta;

        $this->assertSame(0.0, $calculadora->calcularTotal([]));
    }

    public function test_rechaza_una_cantidad_igual_a_cero(): void
    {
        $calculadora = new CalculadoraVenta;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('La cantidad debe ser un entero mayor que cero.');

        $calculadora->calcularSubtotal(24.90, 0);
    }

    public function test_rechaza_una_cantidad_negativa(): void
    {
        $calculadora = new CalculadoraVenta;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('La cantidad debe ser un entero mayor que cero.');

        $calculadora->calcularSubtotal(24.90, -1);
    }

    public function test_rechaza_un_precio_igual_a_cero(): void
    {
        $calculadora = new CalculadoraVenta;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El precio debe ser un valor numérico mayor que cero.');

        $calculadora->calcularSubtotal(0, 1);
    }

    public function test_rechaza_un_precio_negativo(): void
    {
        $calculadora = new CalculadoraVenta;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El precio debe ser un valor numérico mayor que cero.');

        $calculadora->normalizarPrecio(-5);
    }

    public function test_rechaza_un_precio_no_numerico(): void
    {
        $calculadora = new CalculadoraVenta;

        $this->expectException(InvalidArgumentException::class);

        $calculadora->normalizarPrecio('precio-invalido');
    }

    public function test_rechaza_un_subtotal_negativo(): void
    {
        $calculadora = new CalculadoraVenta;

        $this->expectException(InvalidArgumentException::class);

        $calculadora->calcularTotal([10.00, -1.00]);
    }
}
