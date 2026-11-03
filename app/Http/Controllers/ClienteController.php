<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(): View
    {
        $clientes = Cliente::query()
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->paginate(10);

        return view('clientes.index', compact('clientes'));
    }

    public function create(): View
    {
        $cliente = new Cliente();

        return view('clientes.create', compact('cliente'));
    }

    public function store(ClienteRequest $request): RedirectResponse
    {
        Cliente::create($request->validated());

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente registrado correctamente.');
    }

    public function edit(Cliente $cliente): View
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(ClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        $cliente->update($request->validated());

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        if ($cliente->ventas()->exists()) {
            return redirect()
                ->route('clientes.index')
                ->with('error', 'No se puede eliminar el cliente porque tiene ventas asociadas.');
        }

        try {
            $cliente->delete();
        } catch (QueryException) {
            return redirect()
                ->route('clientes.index')
                ->with('error', 'No se puede eliminar el cliente porque tiene registros relacionados.');
        }

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
