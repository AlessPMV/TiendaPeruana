<?php

namespace App\Services;

use InvalidArgumentException;

class CalculadoraVenta
{
    public function normalizarPrecio(int|float|string $precio): float
    {
        return $this->centavos($precio) / 100;
    }

    public function calcularSubtotal(int|float|string $precio, int $cantidad): float
    {
        if ($cantidad <= 0) {
            throw new InvalidArgumentException('La cantidad debe ser un entero mayor que cero.');
        }

        return ($this->centavos($precio) * $cantidad) / 100;
    }

    public function calcularTotal(array $subtotales): float
    {
        $total = 0;

        foreach ($subtotales as $subtotal) {
            if (! is_numeric($subtotal) || (float) $subtotal < 0) {
                throw new InvalidArgumentException('Los subtotales deben ser valores numéricos no negativos.');
            }

            $total += (int) round((float) $subtotal * 100);
        }

        return $total / 100;
    }

    private function centavos(int|float|string $precio): int
    {
        if (! is_numeric($precio) || (float) $precio <= 0) {
            throw new InvalidArgumentException('El precio debe ser un valor numérico mayor que cero.');
        }

        return (int) round((float) $precio * 100);
    }
}
