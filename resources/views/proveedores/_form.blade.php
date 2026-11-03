<div class="form-grid">
    <div class="form-field form-field--full">
        <label for="nombre">Nombre <span class="required" aria-hidden="true">*</span><span class="sr-only"> (obligatorio)</span></label>
        <input
            id="nombre"
            name="nombre"
            type="text"
            value="{{ old('nombre', $proveedor->nombre) }}"
            class="form-input @error('nombre') is-invalid @enderror"
            maxlength="150"
            autocomplete="organization"
            placeholder="Ej. Distribuidora Los Andes"
            required
            @error('nombre') aria-invalid="true" aria-describedby="nombre-error" @enderror
        >
        @error('nombre')
            <p class="form-error" id="nombre-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-field">
        <label for="ruc">RUC <span class="required" aria-hidden="true">*</span><span class="sr-only"> (obligatorio)</span></label>
        <input
            id="ruc"
            name="ruc"
            type="text"
            value="{{ old('ruc', $proveedor->ruc) }}"
            class="form-input @error('ruc') is-invalid @enderror"
            maxlength="20"
            autocomplete="off"
            placeholder="Ej. 20123456789"
            required
            aria-describedby="ruc-help @error('ruc') ruc-error @enderror"
            @error('ruc') aria-invalid="true" @enderror
        >
        <p class="form-help" id="ruc-help">Debe ser único para cada proveedor.</p>
        @error('ruc')
            <p class="form-error" id="ruc-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-field">
        <label for="telefono">Teléfono <span class="form-optional">(opcional)</span></label>
        <input
            id="telefono"
            name="telefono"
            type="tel"
            value="{{ old('telefono', $proveedor->telefono) }}"
            class="form-input @error('telefono') is-invalid @enderror"
            maxlength="20"
            pattern="[0-9+\-()\s]{6,20}"
            autocomplete="tel"
            placeholder="Ej. 987 654 321"
            aria-describedby="telefono-help @error('telefono') telefono-error @enderror"
            @error('telefono') aria-invalid="true" @enderror
        >
        <p class="form-help" id="telefono-help">Entre 6 y 20 caracteres.</p>
        @error('telefono')
            <p class="form-error" id="telefono-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-field form-field--full">
        <label for="email">Correo electrónico <span class="form-optional">(opcional)</span></label>
        <input
            id="email"
            name="email"
            type="email"
            value="{{ old('email', $proveedor->email) }}"
            class="form-input @error('email') is-invalid @enderror"
            maxlength="150"
            autocomplete="email"
            placeholder="ventas@proveedor.com"
            @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
        >
        @error('email')
            <p class="form-error" id="email-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-field form-field--full">
        <label for="direccion">Dirección <span class="form-optional">(opcional)</span></label>
        <textarea
            id="direccion"
            name="direccion"
            class="form-input form-textarea @error('direccion') is-invalid @enderror"
            rows="3"
            maxlength="255"
            autocomplete="street-address"
            @error('direccion') aria-invalid="true" aria-describedby="direccion-error" @enderror
        >{{ old('direccion', $proveedor->direccion) }}</textarea>
        @error('direccion')
            <p class="form-error" id="direccion-error">{{ $message }}</p>
        @enderror
    </div>
</div>
