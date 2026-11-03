<div class="form-grid">
    <div class="form-field">
        <label for="nombres">Nombres <span class="required" aria-hidden="true">*</span><span class="sr-only"> (obligatorio)</span></label>
        <input
            id="nombres"
            name="nombres"
            type="text"
            value="{{ old('nombres', $cliente->nombres) }}"
            class="form-input @error('nombres') is-invalid @enderror"
            maxlength="100"
            autocomplete="given-name"
            required
            @error('nombres') aria-invalid="true" aria-describedby="nombres-error" @enderror
        >
        @error('nombres')
            <p class="form-error" id="nombres-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-field">
        <label for="apellidos">Apellidos <span class="required" aria-hidden="true">*</span><span class="sr-only"> (obligatorio)</span></label>
        <input
            id="apellidos"
            name="apellidos"
            type="text"
            value="{{ old('apellidos', $cliente->apellidos) }}"
            class="form-input @error('apellidos') is-invalid @enderror"
            maxlength="100"
            autocomplete="family-name"
            required
            @error('apellidos') aria-invalid="true" aria-describedby="apellidos-error" @enderror
        >
        @error('apellidos')
            <p class="form-error" id="apellidos-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-field">
        <label for="documento">Documento <span class="required" aria-hidden="true">*</span><span class="sr-only"> (obligatorio)</span></label>
        <input
            id="documento"
            name="documento"
            type="text"
            value="{{ old('documento', $cliente->documento) }}"
            class="form-input @error('documento') is-invalid @enderror"
            maxlength="20"
            autocomplete="off"
            placeholder="Ej. 12345678"
            required
            aria-describedby="documento-help @error('documento') documento-error @enderror"
            @error('documento') aria-invalid="true" @enderror
        >
        <p class="form-help" id="documento-help">Debe ser único para cada cliente.</p>
        @error('documento')
            <p class="form-error" id="documento-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-field">
        <label for="telefono">Teléfono <span class="form-optional">(opcional)</span></label>
        <input
            id="telefono"
            name="telefono"
            type="tel"
            value="{{ old('telefono', $cliente->telefono) }}"
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
            value="{{ old('email', $cliente->email) }}"
            class="form-input @error('email') is-invalid @enderror"
            maxlength="150"
            autocomplete="email"
            placeholder="cliente@correo.com"
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
        >{{ old('direccion', $cliente->direccion) }}</textarea>
        @error('direccion')
            <p class="form-error" id="direccion-error">{{ $message }}</p>
        @enderror
    </div>
</div>
