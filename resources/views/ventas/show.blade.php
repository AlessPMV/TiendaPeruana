@extends('layouts.app')

@section('title', 'Detalle de venta')

@section('content')
    <div class="page">
        <div class="container">
            <nav class="breadcrumb" aria-label="Ruta de navegación">
                <a href="{{ route('home') }}">Inicio</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('ventas.index') }}">Ventas</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">{{ $venta->codigo }}</span>
            </nav>

            <header class="page-head">
                <div>
                    <span class="eyebrow">Detalle de venta</span>
                    <h1>{{ $venta->codigo }}</h1>
                    <p>Resumen de los productos, cantidades e importes registrados.</p>
                </div>
                <div class="page-head__actions">
                    <a class="btn btn--ghost" href="{{ route('ventas.index') }}">Volver al listado</a>
                    <a class="btn btn--primary" href="{{ route('ventas.create') }}">Registrar otra venta</a>
                </div>
            </header>

            <section class="sale-detail-summary" aria-label="Datos generales de la venta">
                <div class="sale-detail-summary__item">
                    <span class="sale-detail-summary__label">Código</span>
                    <strong>{{ $venta->codigo }}</strong>
                </div>
                <div class="sale-detail-summary__item">
                    <span class="sale-detail-summary__label">Cliente</span>
                    <strong>{{ $venta->cliente->nombres }} {{ $venta->cliente->apellidos }}</strong>
                    <small>Doc. {{ $venta->cliente->documento }}</small>
                </div>
                <div class="sale-detail-summary__item">
                    <span class="sale-detail-summary__label">Fecha</span>
                    <strong>{{ $venta->fecha->format('d/m/Y') }}</strong>
                </div>
                <div class="sale-detail-summary__item">
                    <span class="sale-detail-summary__label">Total</span>
                    <strong class="sale-detail-summary__total">S/ {{ number_format((float) $venta->total, 2, '.', ',') }}</strong>
                </div>
            </section>

            <section class="card table-card sale-detail-card" aria-label="Productos de la venta">
                <div class="table-card__header">
                    <div>
                        <h2>Productos vendidos</h2>
                        <p>{{ $venta->detalleVentas->count() }} {{ $venta->detalleVentas->count() === 1 ? 'producto registrado' : 'productos registrados' }}</p>
                    </div>
                </div>

                <div class="table-wrap">
                    <table class="data-table data-table--wide data-table--sale-detail">
                        <caption class="sr-only">Productos vendidos en la venta {{ $venta->codigo }}</caption>
                        <thead>
                            <tr>
                                <th scope="col">Producto</th>
                                <th scope="col">Categoría</th>
                                <th scope="col" class="data-table__number-heading">Cantidad</th>
                                <th scope="col" class="data-table__number-heading">Precio unitario</th>
                                <th scope="col" class="data-table__number-heading">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($venta->detalleVentas as $detalle)
                                <tr>
                                    <td>
                                        <span class="data-table__strong">{{ $detalle->producto->nombre }}</span>
                                        <small class="data-table__meta">Proveedor: {{ $detalle->producto->proveedor?->nombre ?? '—' }}</small>
                                    </td>
                                    <td><span class="data-table__category">{{ $detalle->producto->categoria }}</span></td>
                                    <td class="data-table__price">{{ $detalle->cantidad }}</td>
                                    <td class="data-table__price">S/ {{ number_format((float) $detalle->precio_unitario, 2, '.', ',') }}</td>
                                    <td class="data-table__price">S/ {{ number_format((float) $detalle->subtotal, 2, '.', ',') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th scope="row" colspan="4">Total de la venta</th>
                                <td class="sale-detail-card__total">S/ {{ number_format((float) $venta->total, 2, '.', ',') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>

            <p class="sale-detail-note">
                Los precios unitarios y los importes fueron obtenidos del catálogo y calculados por el servidor al registrar la venta.
            </p>
        </div>
    </div>
@endsection
