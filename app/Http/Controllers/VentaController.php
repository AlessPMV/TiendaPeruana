<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistroVentaRequest;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Services\CalculadoraVenta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VentaController extends Controller
{
    public function index(): View
    {
        $ventas = Venta::query()
            ->with('cliente')
            ->withCount('detalleVentas')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(10);

        return view('ventas.index', compact('ventas'));
    }

    public function create(): View
    {
        $clientes = Cliente::query()
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        $productos = Producto::query()
            ->orderBy('nombre')
            ->get();

        $productosParaVenta = $productos
            ->map(fn (Producto $producto) => [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => (float) $producto->precio,
                'stock' => $producto->stock,
            ])
            ->values();

        return view('ventas.create', compact('clientes', 'productos', 'productosParaVenta'));
    }

    public function store(RegistroVentaRequest $request, CalculadoraVenta $calculadora): RedirectResponse
    {
        $datos = $request->validated();

        $venta = DB::transaction(function () use ($datos, $calculadora) {
            $cantidades = collect($datos['productos'])
                ->groupBy('producto_id')
                ->map(fn ($lineas) => (int) $lineas->sum('cantidad'));

            $productos = Producto::query()
                ->whereIn('id', $cantidades->keys())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $errores = [];

            foreach ($datos['productos'] as $indice => $linea) {
                if (! $productos->has($linea['producto_id'])) {
                    $errores["productos.$indice.producto_id"] = 'El producto seleccionado no existe.';
                }
            }

            foreach ($cantidades as $productoId => $cantidad) {
                $producto = $productos->get($productoId);

                if ($producto && $producto->stock < $cantidad) {
                    $errores['productos'] = "No hay stock suficiente para {$producto->nombre}. Stock disponible: {$producto->stock}.";
                    break;
                }
            }

            if ($errores !== []) {
                throw ValidationException::withMessages($errores);
            }

            $detalles = [];
            $subtotales = [];

            foreach ($datos['productos'] as $linea) {
                $producto = $productos->get($linea['producto_id']);
                $cantidad = (int) $linea['cantidad'];
                $precioUnitario = $calculadora->normalizarPrecio($producto->precio);
                $subtotal = $calculadora->calcularSubtotal($producto->precio, $cantidad);

                $detalles[] = [
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotal,
                ];

                $subtotales[] = $subtotal;
            }

            $venta = Venta::create([
                'cliente_id' => $datos['cliente_id'],
                'fecha' => now()->toDateString(),
                'total' => $calculadora->calcularTotal($subtotales),
            ]);

            $venta->detalleVentas()->createMany($detalles);

            foreach ($cantidades as $productoId => $cantidad) {
                $productos->get($productoId)->decrement('stock', $cantidad);
            }

            return $venta;
        });

        return redirect()
            ->route('ventas.show', $venta)
            ->with('success', 'Venta registrada correctamente.');
    }

    public function show(Venta $venta): View
    {
        $venta->load(['cliente', 'detalleVentas.producto.proveedor']);

        return view('ventas.show', compact('venta'));
    }
}
