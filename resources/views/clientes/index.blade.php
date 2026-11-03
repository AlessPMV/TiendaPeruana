@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
    <div class="page">
        <div class="container">
            <nav class="breadcrumb" aria-label="Ruta de navegación">
                <a href="{{ route('home') }}">Inicio</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Clientes</span>
            </nav>

            <header class="page-head">
                <div>
                    <span class="eyebrow">Módulo de clientes</span>
                    <h1>Clientes</h1>
                    <p>Consulta y administra los clientes registrados en la tienda.</p>
                </div>
                <a class="btn btn--primary" href="{{ route('clientes.create') }}">
                    <svg class="btn__icon" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    Nuevo cliente
                </a>
            </header>

            <section class="card table-card" aria-label="Listado de clientes">
                <div class="table-card__header">
                    <div>
                        <h2>Listado de clientes</h2>
                        <p>{{ $clientes->total() }} {{ $clientes->total() === 1 ? 'cliente registrado' : 'clientes registrados' }}</p>
                    </div>
                </div>

                @if ($clientes->isEmpty())
                    <div class="empty-state">
                        <span class="empty-state__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="8" r="3.4" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M5 19c.7-3.2 3.4-5 7-5s6.3 1.8 7 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <h2>Aún no hay clientes registrados</h2>
                        <p>Registra el primer cliente para comenzar a gestionar la información comercial de la tienda.</p>
                        <a class="btn btn--primary" href="{{ route('clientes.create') }}">Registrar primer cliente</a>
                    </div>
                @else
                    <div class="table-wrap">
                        <table class="data-table">
                            <caption class="sr-only">Listado de clientes registrados</caption>
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Nombres</th>
                                    <th scope="col">Apellidos</th>
                                    <th scope="col">Documento</th>
                                    <th scope="col">Teléfono</th>
                                    <th scope="col">Correo electrónico</th>
                                    <th scope="col" class="data-table__actions-heading">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clientes as $cliente)
                                    <tr>
                                        <td class="data-table__id">#{{ $cliente->id }}</td>
                                        <td><span class="data-table__strong">{{ $cliente->nombres }}</span></td>
                                        <td>{{ $cliente->apellidos }}</td>
                                        <td><span class="data-table__document">{{ $cliente->documento }}</span></td>
                                        <td>{{ $cliente->telefono ?: '—' }}</td>
                                        <td>
                                            @if ($cliente->email)
                                                <a class="data-table__link" href="mailto:{{ $cliente->email }}">{{ $cliente->email }}</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            <div class="table-actions">
                                                <a class="btn btn--sm btn--outline" href="{{ route('clientes.edit', $cliente) }}" aria-label="Editar cliente {{ $cliente->nombres }} {{ $cliente->apellidos }}">Editar</a>
                                                <form
                                                    action="{{ route('clientes.destroy', $cliente) }}"
                                                    method="POST"
                                                    data-confirm="¿Eliminar al cliente {{ $cliente->nombres }} {{ $cliente->apellidos }}? Esta acción no se puede deshacer."
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn--sm btn--danger" type="submit" aria-label="Eliminar cliente {{ $cliente->nombres }} {{ $cliente->apellidos }}">Eliminar</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($clientes->hasPages())
                        <div class="pagination">
                            <span class="pagination__summary">
                                Mostrando {{ $clientes->firstItem() }}–{{ $clientes->lastItem() }} de {{ $clientes->total() }}
                            </span>
                            <nav class="pagination__links" aria-label="Paginación de clientes">
                                @if ($clientes->onFirstPage())
                                    <span class="pagination__link is-disabled">Anterior</span>
                                @else
                                    <a class="pagination__link" href="{{ $clientes->previousPageUrl() }}" rel="prev">Anterior</a>
                                @endif

                                <span class="pagination__current" aria-current="page">Página {{ $clientes->currentPage() }} de {{ $clientes->lastPage() }}</span>

                                @if ($clientes->hasMorePages())
                                    <a class="pagination__link" href="{{ $clientes->nextPageUrl() }}" rel="next">Siguiente</a>
                                @else
                                    <span class="pagination__link is-disabled">Siguiente</span>
                                @endif
                            </nav>
                        </div>
                    @endif
                @endif
            </section>
        </div>
    </div>
@endsection
