<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoRequest;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductoController extends Controller
{
    public function index(): View
    {
        $productos = Producto::query()
            ->with('proveedor')
            ->orderBy('nombre')
            ->paginate(10);

        return view('productos.index', compact('productos'));
    }

    public function create(): View
    {
        $producto = new Producto();
        $proveedores = Proveedor::query()->orderBy('nombre')->get();

        return view('productos.create', compact('producto', 'proveedores'));
    }

    public function store(ProductoRequest $request): RedirectResponse
    {
        Producto::create($request->validated());

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto registrado correctamente.');
    }

    public function edit(Producto $producto): View
    {
        $proveedores = Proveedor::query()->orderBy('nombre')->get();

        return view('productos.edit', compact('producto', 'proveedores'));
    }

    public function update(ProductoRequest $request, Producto $producto): RedirectResponse
    {
        $producto->update($request->validated());

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto): RedirectResponse
    {
        if ($producto->detalleVentas()->exists()) {
            return redirect()
                ->route('productos.index')
                ->with('error', 'No se puede eliminar el producto porque está asociado a detalles de venta.');
        }

        try {
            $producto->delete();
        } catch (QueryException) {
            return redirect()
                ->route('productos.index')
                ->with('error', 'No se puede eliminar el producto porque tiene registros relacionados.');
        }

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}
