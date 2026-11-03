@php
    $categoriasSugeridas = [
        'Abarrotes',
        'Bebidas',
        'Lácteos',
        'Limpieza',
        'Cuidado personal',
        'Frutas y verduras',
        'Snacks',
        'Productos congelados',
    ];
@endphp

<div class="form-grid">
    <div class="form-field form-field--full">
        <label for="nombre">Nombre <span class="required" aria-hidden="true">*</span><span class="sr-only"> (obligatorio)</span></label>
        <input
            id="nombre"
            name="nombre"
            type="text"
            value="{{ old('nombre', $producto->nombre) }}"
            class="form-input @error('nombre') is-invalid @enderror"
            maxlength="150"
            autocomplete="off"
            placeholder="Ej. Arroz extra 5 kg"
            required
            @error('nombre') aria-invalid="true" aria-describedby="nombre-error" @enderror
        >
        @error('nombre')
            <p class="form-error" id="nombre-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-field form-field--full">
        <label for="descripcion">Descripción <span class="form-optional">(opcional)</span></label>
        <textarea
            id="descripcion"
            name="descripcion"
            class="form-input form-textarea @error('descripcion') is-invalid @enderror"
            rows="3"
            placeholder="Características o presentación del producto"
            @error('descripcion') aria-invalid="true" aria-describedby="descripcion-error" @enderror
        >{{ old('descripcion', $producto->descripcion) }}</textarea>
        @error('descripcion')
            <p class="form-error" id="descripcion-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-field">
        <label for="categoria">Categoría <span class="required" aria-hidden="true">*</span><span class="sr-only"> (obligatorio)</span></label>
        <input
            id="categoria"
            name="categoria"
            type="text"
            value="{{ old('categoria', $producto->categoria) }}"
            class="form-input @error('categoria') is-invalid @enderror"
            list="categorias-producto"
            maxlength="100"
            autocomplete="off"
            placeholder="Ej. Abarrotes"
            required
            aria-describedby="categoria-help @error('categoria') categoria-error @enderror"
            @error('categoria') aria-invalid="true" @enderror
        >
        <datalist id="categorias-producto">
            @foreach ($categoriasSugeridas as $categoria)
                <option value="{{ $categoria }}"></option>
            @endforeach
        </datalist>
        <p class="form-help" id="categoria-help">Elige una categoría sugerida o escribe otra.</p>
        @error('categoria')
            <p class="form-error" id="categoria-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-field">
        <label for="proveedor_id">Proveedor <span class="required" aria-hidden="true">*</span><span class="sr-only"> (obligatorio)</span></label>
        <select
            id="proveedor_id"
            name="proveedor_id"
            class="form-input @error('proveedor_id') is-invalid @enderror"
            required
            @error('proveedor_id') aria-invalid="true" aria-describedby="proveedor_id-error" @enderror
        >
            <option value="">Selecciona un proveedor</option>
            @foreach ($proveedores as $proveedor)
                <option value="{{ $proveedor->id }}" @selected((string) old('proveedor_id', $producto->proveedor_id) === (string) $proveedor->id)>
                    {{ $proveedor->nombre }} — RUC {{ $proveedor->ruc }}
                </option>
            @endforeach
        </select>
        @error('proveedor_id')
            <p class="form-error" id="proveedor_id-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-field">
        <label for="precio">Precio (S/) <span class="required" aria-hidden="true">*</span><span class="sr-only"> (obligatorio)</span></label>
        <input
            id="precio"
            name="precio"
            type="number"
            value="{{ old('precio', $producto->precio) }}"
            class="form-input @error('precio') is-invalid @enderror"
            min="0.01"
            step="0.01"
            inputmode="decimal"
            placeholder="0.00"
            required
            aria-describedby="precio-help @error('precio') precio-error @enderror"
            @error('precio') aria-invalid="true" @enderror
        >
        <p class="form-help" id="precio-help">Debe ser mayor que cero.</p>
        @error('precio')
            <p class="form-error" id="precio-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-field">
        <label for="stock">Stock <span class="required" aria-hidden="true">*</span><span class="sr-only"> (obligatorio)</span></label>
        <input
            id="stock"
            name="stock"
            type="number"
            value="{{ old('stock', $producto->stock) }}"
            class="form-input @error('stock') is-invalid @enderror"
            min="0"
            step="1"
            inputmode="numeric"
            placeholder="0"
            required
            aria-describedby="stock-help @error('stock') stock-error @enderror"
            @error('stock') aria-invalid="true" @enderror
        >
        <p class="form-help" id="stock-help">Cantidad disponible en unidades.</p>
        @error('stock')
            <p class="form-error" id="stock-error">{{ $message }}</p>
        @enderror
    </div>
</div>
