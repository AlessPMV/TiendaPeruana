@extends('layouts.app')

@section('title', 'Nueva venta')

@section('content')
    @php
        $lineas = old('productos', [['producto_id' => '', 'cantidad' => 1]]);

        if (! is_array($lineas) || $lineas === []) {
            $lineas = [['producto_id' => '', 'cantidad' => 1]];
        }

        $hayStock = $productos->contains(fn ($producto) => $producto->stock > 0);
        $puedeRegistrar = $clientes->isNotEmpty() && $hayStock;
    @endphp

    <div class="page">
        <div class="container">
            <nav class="breadcrumb" aria-label="Ruta de navegación">
                <a href="{{ route('home') }}">Inicio</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('ventas.index') }}">Ventas</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Nueva venta</span>
            </nav>

            <header class="page-head">
                <div>
                    <span class="eyebrow">Módulo de ventas</span>
                    <h1>Nueva venta</h1>
                    <p>Selecciona un cliente y los productos vendidos. Los importes se calculan automáticamente.</p>
                </div>
            </header>

            <div class="form-card sale-form-card">
                @if ($clientes->isEmpty())
                    <div class="alert alert--error" role="alert">
                        <svg class="alert__icon" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M10 6v5m0 3.2v.1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="10" cy="10" r="7.5" stroke="currentColor" stroke-width="1.7"/>
                        </svg>
                        <div>
                            <strong>No hay clientes registrados.</strong>
                            <p>Para registrar una venta primero debes <a class="data-table__link" href="{{ route('clientes.create') }}">crear un cliente</a>.</p>
                        </div>
                    </div>
                @endif

                @if ($productos->isEmpty())
                    <div class="alert alert--error" role="alert">
                        <svg class="alert__icon" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M10 6v5m0 3.2v.1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="10" cy="10" r="7.5" stroke="currentColor" stroke-width="1.7"/>
                        </svg>
                        <div>
                            <strong>No hay productos registrados.</strong>
                            <p>Para registrar una venta primero debes <a class="data-table__link" href="{{ route('productos.create') }}">crear un producto</a>.</p>
                        </div>
                    </div>
                @elseif (! $hayStock)
                    <div class="alert alert--error" role="alert">
                        <svg class="alert__icon" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M10 6v5m0 3.2v.1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="10" cy="10" r="7.5" stroke="currentColor" stroke-width="1.7"/>
                        </svg>
                        <div>
                            <strong>No hay productos con stock disponible.</strong>
                            <p>Actualiza el stock del catálogo antes de registrar una venta.</p>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert--error" role="alert">
                        <svg class="alert__icon" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M10 6v5m0 3.2v.1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="10" cy="10" r="7.5" stroke="currentColor" stroke-width="1.7"/>
                        </svg>
                        <div>
                            <strong>No se pudo registrar la venta.</strong>
                            <p>Revisa los campos marcados e inténtalo nuevamente.</p>
                        </div>
                    </div>
                @endif

                <form action="{{ route('ventas.store') }}" method="POST" data-sale-form>
                    @csrf

                    <div class="sale-client">
                        <div class="form-field">
                            <label for="cliente_id">Cliente <span class="required" aria-hidden="true">*</span><span class="sr-only"> (obligatorio)</span></label>
                            <select
                                id="cliente_id"
                                name="cliente_id"
                                class="form-input @error('cliente_id') is-invalid @enderror"
                                required
                                @error('cliente_id') aria-invalid="true" aria-describedby="cliente_id-error" @enderror
                            >
                                <option value="">Selecciona un cliente</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" @selected((string) old('cliente_id') === (string) $cliente->id)>
                                        {{ $cliente->nombres }} {{ $cliente->apellidos }} — Doc. {{ $cliente->documento }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                                <p class="form-error" id="cliente_id-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <section class="sale-lines" aria-labelledby="productos-venta-title">
                        <div class="sale-lines__heading">
                            <div>
                                <h2 id="productos-venta-title">Productos de la venta</h2>
                                <p id="productos-venta-help">Agrega uno o varios productos e indica la cantidad vendida. La cantidad no puede superar el stock disponible.</p>
                            </div>
                            <button class="btn btn--sm btn--outline" type="button" data-add-line @disabled(! $hayStock)>
                                <svg class="btn__icon" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                Agregar producto
                            </button>
                        </div>

                        @error('productos')
                            <div class="alert alert--error sale-lines__alert" role="alert">
                                <svg class="alert__icon" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M10 6v5m0 3.2v.1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <circle cx="10" cy="10" r="7.5" stroke="currentColor" stroke-width="1.7"/>
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror

                        <div
                            class="sale-lines__rows"
                            data-sale-rows
                            data-next-index="{{ count($lineas) }}"
                            role="group"
                            aria-labelledby="productos-venta-title"
                            aria-describedby="productos-venta-help"
                        >
                            @foreach ($lineas as $index => $item)
                                @include('ventas._line', [
                                    'index' => $index,
                                    'item' => is_array($item) ? $item : [],
                                ])
                            @endforeach
                        </div>
                        <p class="sr-only" role="status" aria-live="polite" data-sale-announcer></p>
                    </section>

                    <div class="sale-summary">
                        <div>
                            <span class="sale-summary__label" id="sale-total-label">Total de la venta</span>
                            <p class="sale-summary__hint">El precio unitario se obtiene del catálogo y el total se calcula en el servidor.</p>
                        </div>
                        <output
                            class="sale-summary__value"
                            data-sale-total
                            aria-labelledby="sale-total-label"
                            aria-live="polite"
                            aria-atomic="true"
                        >S/ 0.00</output>
                    </div>

                    <p class="form-required-note">Los campos marcados con <span aria-hidden="true">*</span><span class="sr-only">asterisco</span> son obligatorios.</p>

                    <div class="form-actions">
                        <a class="btn btn--ghost" href="{{ route('ventas.index') }}">Cancelar</a>
                        <button class="btn btn--primary" type="submit" @disabled(! $puedeRegistrar)>Registrar venta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <template id="sale-line-template">
        <div class="sale-line" data-sale-line>
            <div class="sale-line__field sale-line__field--product">
                <label for="producto___INDEX__">Producto <span class="required" aria-hidden="true">*</span><span class="sr-only"> (obligatorio)</span></label>
                <select
                    id="producto___INDEX__"
                    name="productos[__INDEX__][producto_id]"
                    class="form-input"
                    data-product-select
                    required
                >
                    <option value="">Selecciona un producto</option>
                </select>
            </div>

            <div class="sale-line__field">
                <label for="cantidad___INDEX__">Cantidad <span class="required" aria-hidden="true">*</span><span class="sr-only"> (obligatorio)</span></label>
                <input
                    id="cantidad___INDEX__"
                    name="productos[__INDEX__][cantidad]"
                    type="number"
                    value="1"
                    class="form-input"
                    min="1"
                    step="1"
                    inputmode="numeric"
                    data-quantity
                    required
                    aria-describedby="cantidad___INDEX__-help"
                >
                <p class="form-help" id="cantidad___INDEX__-help">Máximo según el stock del producto.</p>
            </div>

            <div class="sale-line__field">
                <span class="sale-line__label">Precio unitario</span>
                <span class="sale-line__value" data-unit-price>S/ 0.00</span>
            </div>

            <div class="sale-line__field">
                <span class="sale-line__label">Subtotal</span>
                <span class="sale-line__value sale-line__value--strong" data-subtotal>S/ 0.00</span>
            </div>

            <div class="sale-line__actions">
                <button class="btn btn--sm btn--ghost" type="button" data-remove-line aria-label="Quitar producto de la venta">Quitar</button>
            </div>
        </div>
    </template>

    <script type="application/json" id="venta-productos-data">@json($productosParaVenta)</script>
@endsection

@push('scripts')
    <script src="{{ asset('js/ventas.js') }}" defer></script>
@endpush
