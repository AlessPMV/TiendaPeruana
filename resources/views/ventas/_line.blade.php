@php
    $productoSeleccionado = $productos->firstWhere('id', (int) ($item['producto_id'] ?? 0));
    $cantidad = old("productos.$index.cantidad", $item['cantidad'] ?? 1);
    $precio = $productoSeleccionado ? (float) $productoSeleccionado->precio : 0;
    $cantidadCalculo = is_numeric($cantidad) && (int) $cantidad > 0 ? (int) $cantidad : 0;
    $subtotal = $precio * $cantidadCalculo;
@endphp

<div class="sale-line" data-sale-line>
    <div class="sale-line__field sale-line__field--product">
        <label for="producto_{{ $index }}">Producto <span class="required" aria-hidden="true">*</span><span class="sr-only"> (obligatorio)</span></label>
        <select
            id="producto_{{ $index }}"
            name="productos[{{ $index }}][producto_id]"
            class="form-input @error("productos.$index.producto_id") is-invalid @enderror"
            data-product-select
            data-selected="{{ old("productos.$index.producto_id", $item['producto_id'] ?? '') }}"
            required
            @error("productos.$index.producto_id") aria-invalid="true" aria-describedby="producto_{{ $index }}-error" @enderror
        >
            <option value="">Selecciona un producto</option>
            @foreach ($productos as $producto)
                <option
                    value="{{ $producto->id }}"
                    data-price="{{ number_format((float) $producto->precio, 2, '.', '') }}"
                    data-stock="{{ $producto->stock }}"
                    data-nombre="{{ $producto->nombre }}"
                    @selected((string) old("productos.$index.producto_id", $item['producto_id'] ?? '') === (string) $producto->id)
                    @disabled($producto->stock === 0)
                >
                    {{ $producto->nombre }} — S/ {{ number_format((float) $producto->precio, 2, '.', ',') }} (Stock: {{ $producto->stock }})
                </option>
            @endforeach
        </select>
        @error("productos.$index.producto_id")
            <p class="form-error" id="producto_{{ $index }}-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="sale-line__field">
        <label for="cantidad_{{ $index }}">Cantidad <span class="required" aria-hidden="true">*</span><span class="sr-only"> (obligatorio)</span></label>
        <input
            id="cantidad_{{ $index }}"
            name="productos[{{ $index }}][cantidad]"
            type="number"
            value="{{ $cantidad }}"
            class="form-input @error("productos.$index.cantidad") is-invalid @enderror"
            min="1"
            step="1"
            inputmode="numeric"
            data-quantity
            required
            aria-describedby="cantidad_{{ $index }}-help @error("productos.$index.cantidad") cantidad_{{ $index }}-error @enderror"
            @error("productos.$index.cantidad") aria-invalid="true" @enderror
        >
        <p class="form-help" id="cantidad_{{ $index }}-help">Máximo según el stock del producto.</p>
        @error("productos.$index.cantidad")
            <p class="form-error" id="cantidad_{{ $index }}-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="sale-line__field">
        <span class="sale-line__label">Precio unitario</span>
        <span class="sale-line__value" data-unit-price>S/ {{ number_format($precio, 2, '.', ',') }}</span>
    </div>

    <div class="sale-line__field">
        <span class="sale-line__label">Subtotal</span>
        <span class="sale-line__value sale-line__value--strong" data-subtotal>S/ {{ number_format($subtotal, 2, '.', ',') }}</span>
    </div>

    <div class="sale-line__actions">
        <button class="btn btn--sm btn--ghost" type="button" data-remove-line aria-label="Quitar producto de la venta">Quitar</button>
    </div>
</div>
