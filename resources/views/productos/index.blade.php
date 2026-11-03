@extends('layouts.app')

@section('title', 'Productos')

@section('content')
    <div class="page">
        <div class="container">
            <nav class="breadcrumb" aria-label="Ruta de navegación">
                <a href="{{ route('home') }}">Inicio</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Productos</span>
            </nav>

            <header class="page-head">
                <div>
                    <span class="eyebrow">Módulo de productos</span>
                    <h1>Productos</h1>
                    <p>Consulta y administra el catálogo de productos de la tienda.</p>
                </div>
                <a class="btn btn--primary" href="{{ route('productos.create') }}">
                    <svg class="btn__icon" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    Nuevo producto
                </a>
            </header>

            <section class="card table-card" aria-label="Listado de productos">
                <div class="table-card__header">
                    <div>
                        <h2>Listado de productos</h2>
                        <p>{{ $productos->total() }} {{ $productos->total() === 1 ? 'producto registrado' : 'productos registrados' }}</p>
                    </div>
                </div>

                @if ($productos->isEmpty())
                    <div class="empty-state">
                        <span class="empty-state__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <path d="m4 7.5 8 4.5 8-4.5M12 12v9" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <h2>Aún no hay productos registrados</h2>
                        <p>Registra el primer producto del catálogo para comenzar a organizar la información comercial.</p>
                        <a class="btn btn--primary" href="{{ route('productos.create') }}">Registrar primer producto</a>
                    </div>
                @else
                    <div class="table-wrap">
                        <table class="data-table data-table--wide data-table--products">
                            <caption class="sr-only">Listado de productos del catálogo</caption>
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Descripción</th>
                                    <th scope="col">Categoría</th>
                                    <th scope="col" class="data-table__number-heading">Precio</th>
                                    <th scope="col" class="data-table__number-heading">Stock</th>
                                    <th scope="col">Proveedor</th>
                                    <th scope="col" class="data-table__actions-heading">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productos as $producto)
                                    <tr>
                                        <td class="data-table__id">#{{ $producto->id }}</td>
                                        <td><span class="data-table__strong">{{ $producto->nombre }}</span></td>
                                        <td class="data-table__description">
                                            {{ $producto->descripcion ? \Illuminate\Support\Str::limit($producto->descripcion, 70) : '—' }}
                                        </td>
                                        <td><span class="data-table__category">{{ $producto->categoria }}</span></td>
                                        <td class="data-table__price">S/ {{ number_format((float) $producto->precio, 2, '.', ',') }}</td>
                                        <td>
                                            <span class="stock-pill {{ $producto->stock === 0 ? 'stock-pill--out' : ($producto->stock <= 10 ? 'stock-pill--low' : '') }}">
                                                {{ $producto->stock }}
                                            </span>
                                        </td>
                                        <td><span class="data-table__provider">{{ $producto->proveedor?->nombre ?? '—' }}</span></td>
                                        <td>
                                            <div class="table-actions">
                                                <a class="btn btn--sm btn--outline" href="{{ route('productos.edit', $producto) }}" aria-label="Editar producto {{ $producto->nombre }}">Editar</a>
                                                <form
                                                    action="{{ route('productos.destroy', $producto) }}"
                                                    method="POST"
                                                    data-confirm="¿Eliminar el producto {{ $producto->nombre }}? Esta acción no se puede deshacer."
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn--sm btn--danger" type="submit" aria-label="Eliminar producto {{ $producto->nombre }}">Eliminar</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($productos->hasPages())
                        <div class="pagination">
                            <span class="pagination__summary">
                                Mostrando {{ $productos->firstItem() }}–{{ $productos->lastItem() }} de {{ $productos->total() }}
                            </span>
                            <nav class="pagination__links" aria-label="Paginación de productos">
                                @if ($productos->onFirstPage())
                                    <span class="pagination__link is-disabled">Anterior</span>
                                @else
                                    <a class="pagination__link" href="{{ $productos->previousPageUrl() }}" rel="prev">Anterior</a>
                                @endif

                                <span class="pagination__current" aria-current="page">Página {{ $productos->currentPage() }} de {{ $productos->lastPage() }}</span>

                                @if ($productos->hasMorePages())
                                    <a class="pagination__link" href="{{ $productos->nextPageUrl() }}" rel="next">Siguiente</a>
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
