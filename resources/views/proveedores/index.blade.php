@extends('layouts.app')

@section('title', 'Proveedores')

@section('content')
    <div class="page">
        <div class="container">
            <nav class="breadcrumb" aria-label="Ruta de navegación">
                <a href="{{ route('home') }}">Inicio</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Proveedores</span>
            </nav>

            <header class="page-head">
                <div>
                    <span class="eyebrow">Módulo de proveedores</span>
                    <h1>Proveedores</h1>
                    <p>Consulta y administra los proveedores que abastecen a la tienda.</p>
                </div>
                <a class="btn btn--primary" href="{{ route('proveedores.create') }}">
                    <svg class="btn__icon" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    Nuevo proveedor
                </a>
            </header>

            <section class="card table-card" aria-label="Listado de proveedores">
                <div class="table-card__header">
                    <div>
                        <h2>Listado de proveedores</h2>
                        <p>{{ $proveedores->total() }} {{ $proveedores->total() === 1 ? 'proveedor registrado' : 'proveedores registrados' }}</p>
                    </div>
                </div>

                @if ($proveedores->isEmpty())
                    <div class="empty-state">
                        <span class="empty-state__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M4 9h16l-1.4 9.2a2 2 0 0 1-2 1.8H7.4a2 2 0 0 1-2-1.8L4 9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <path d="M8 9V7a4 4 0 0 1 8 0v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <h2>Aún no hay proveedores registrados</h2>
                        <p>Registra el primer proveedor para vincularlo con los productos de la tienda.</p>
                        <a class="btn btn--primary" href="{{ route('proveedores.create') }}">Registrar primer proveedor</a>
                    </div>
                @else
                    <div class="table-wrap">
                        <table class="data-table data-table--wide">
                            <caption class="sr-only">Listado de proveedores registrados</caption>
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">RUC</th>
                                    <th scope="col">Teléfono</th>
                                    <th scope="col">Correo electrónico</th>
                                    <th scope="col">Dirección</th>
                                    <th scope="col" class="data-table__actions-heading">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($proveedores as $proveedor)
                                    <tr>
                                        <td class="data-table__id">#{{ $proveedor->id }}</td>
                                        <td><span class="data-table__strong">{{ $proveedor->nombre }}</span></td>
                                        <td><span class="data-table__document">{{ $proveedor->ruc }}</span></td>
                                        <td>{{ $proveedor->telefono ?: '—' }}</td>
                                        <td>
                                            @if ($proveedor->email)
                                                <a class="data-table__link" href="mailto:{{ $proveedor->email }}">{{ $proveedor->email }}</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="data-table__address">{{ $proveedor->direccion ?: '—' }}</td>
                                        <td>
                                            <div class="table-actions">
                                                <a class="btn btn--sm btn--outline" href="{{ route('proveedores.edit', $proveedor) }}" aria-label="Editar proveedor {{ $proveedor->nombre }}">Editar</a>
                                                <form
                                                    action="{{ route('proveedores.destroy', $proveedor) }}"
                                                    method="POST"
                                                    data-confirm="¿Eliminar al proveedor {{ $proveedor->nombre }}? Esta acción no se puede deshacer."
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn--sm btn--danger" type="submit" aria-label="Eliminar proveedor {{ $proveedor->nombre }}">Eliminar</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($proveedores->hasPages())
                        <div class="pagination">
                            <span class="pagination__summary">
                                Mostrando {{ $proveedores->firstItem() }}–{{ $proveedores->lastItem() }} de {{ $proveedores->total() }}
                            </span>
                            <nav class="pagination__links" aria-label="Paginación de proveedores">
                                @if ($proveedores->onFirstPage())
                                    <span class="pagination__link is-disabled">Anterior</span>
                                @else
                                    <a class="pagination__link" href="{{ $proveedores->previousPageUrl() }}" rel="prev">Anterior</a>
                                @endif

                                <span class="pagination__current" aria-current="page">Página {{ $proveedores->currentPage() }} de {{ $proveedores->lastPage() }}</span>

                                @if ($proveedores->hasMorePages())
                                    <a class="pagination__link" href="{{ $proveedores->nextPageUrl() }}" rel="next">Siguiente</a>
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
