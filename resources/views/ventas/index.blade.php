@extends('layouts.app')

@section('title', 'Ventas')

@section('content')
    <div class="page">
        <div class="container">
            <nav class="breadcrumb" aria-label="Ruta de navegación">
                <a href="{{ route('home') }}">Inicio</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Ventas</span>
            </nav>

            <header class="page-head">
                <div>
                    <span class="eyebrow">Módulo de ventas</span>
                    <h1>Ventas</h1>
                    <p>Consulta las ventas registradas y accede al detalle de cada operación.</p>
                </div>
                <a class="btn btn--primary" href="{{ route('ventas.create') }}">
                    <svg class="btn__icon" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    Nueva venta
                </a>
            </header>

            <section class="card table-card" aria-label="Listado de ventas">
                <div class="table-card__header">
                    <div>
                        <h2>Listado de ventas</h2>
                        <p>{{ $ventas->total() }} {{ $ventas->total() === 1 ? 'venta registrada' : 'ventas registradas' }}</p>
                    </div>
                </div>

                @if ($ventas->isEmpty())
                    <div class="empty-state">
                        <span class="empty-state__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <path d="M9 8h6M9 12h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <h2>Aún no hay ventas registradas</h2>
                        <p>Registra la primera venta para comenzar a llevar el control de las operaciones de la tienda.</p>
                        <a class="btn btn--primary" href="{{ route('ventas.create') }}">Registrar primera venta</a>
                    </div>
                @else
                    <div class="table-wrap">
                        <table class="data-table data-table--wide data-table--sales">
                            <caption class="sr-only">Listado de ventas registradas</caption>
                            <thead>
                                <tr>
                                    <th scope="col">Código</th>
                                    <th scope="col">Cliente</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col" class="data-table__number-heading">Productos</th>
                                    <th scope="col" class="data-table__number-heading">Total</th>
                                    <th scope="col" class="data-table__actions-heading">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ventas as $venta)
                                    <tr>
                                        <td><span class="data-table__document">{{ $venta->codigo }}</span></td>
                                        <td>
                                            <span class="data-table__strong">{{ $venta->cliente->nombres }} {{ $venta->cliente->apellidos }}</span>
                                            <small class="data-table__meta">Doc. {{ $venta->cliente->documento }}</small>
                                        </td>
                                        <td>{{ $venta->fecha->format('d/m/Y') }}</td>
                                        <td class="data-table__price">{{ $venta->detalle_ventas_count }}</td>
                                        <td class="data-table__price">S/ {{ number_format((float) $venta->total, 2, '.', ',') }}</td>
                                        <td>
                                            <div class="table-actions">
                                                <a class="btn btn--sm btn--outline" href="{{ route('ventas.show', $venta) }}" aria-label="Ver detalle de la venta {{ $venta->codigo }}">Ver detalle</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($ventas->hasPages())
                        <div class="pagination">
                            <span class="pagination__summary">
                                Mostrando {{ $ventas->firstItem() }}–{{ $ventas->lastItem() }} de {{ $ventas->total() }}
                            </span>
                            <nav class="pagination__links" aria-label="Paginación de ventas">
                                @if ($ventas->onFirstPage())
                                    <span class="pagination__link is-disabled">Anterior</span>
                                @else
                                    <a class="pagination__link" href="{{ $ventas->previousPageUrl() }}" rel="prev">Anterior</a>
                                @endif

                                <span class="pagination__current" aria-current="page">Página {{ $ventas->currentPage() }} de {{ $ventas->lastPage() }}</span>

                                @if ($ventas->hasMorePages())
                                    <a class="pagination__link" href="{{ $ventas->nextPageUrl() }}" rel="next">Siguiente</a>
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
