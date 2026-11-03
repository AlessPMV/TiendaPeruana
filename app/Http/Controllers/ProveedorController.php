<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProveedorRequest;
use App\Models\Proveedor;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProveedorController extends Controller
{
    public function index(): View
    {
        $proveedores = Proveedor::query()
            ->orderBy('nombre')
            ->paginate(10);

        return view('proveedores.index', compact('proveedores'));
    }

    public function create(): View
    {
        $proveedor = new Proveedor();

        return view('proveedores.create', compact('proveedor'));
    }

    public function store(ProveedorRequest $request): RedirectResponse
    {
        Proveedor::create($request->validated());

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'Proveedor registrado correctamente.');
    }

    public function edit(Proveedor $proveedor): View
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(ProveedorRequest $request, Proveedor $proveedor): RedirectResponse
    {
        $proveedor->update($request->validated());

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor): RedirectResponse
    {
        if ($proveedor->productos()->exists()) {
            return redirect()
                ->route('proveedores.index')
                ->with('error', 'No se puede eliminar el proveedor porque tiene productos asociados.');
        }

        try {
            $proveedor->delete();
        } catch (QueryException) {
            return redirect()
                ->route('proveedores.index')
                ->with('error', 'No se puede eliminar el proveedor porque tiene registros relacionados.');
        }

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }
}
