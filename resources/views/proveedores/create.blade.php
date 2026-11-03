@extends('layouts.app')

@section('title', 'Nuevo proveedor')

@section('content')
    <div class="page">
        <div class="container container--narrow">
            <nav class="breadcrumb" aria-label="Ruta de navegación">
                <a href="{{ route('home') }}">Inicio</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('proveedores.index') }}">Proveedores</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Nuevo proveedor</span>
            </nav>

            <header class="page-head">
                <div>
                    <span class="eyebrow">Módulo de proveedores</span>
                    <h1>Nuevo proveedor</h1>
                    <p>Completa los datos para registrar un nuevo proveedor en el sistema.</p>
                </div>
            </header>

            <div class="form-card">
                @if ($errors->any())
                    <div class="alert alert--error" role="alert">
                        <svg class="alert__icon" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M10 6v5m0 3.2v.1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="10" cy="10" r="7.5" stroke="currentColor" stroke-width="1.7"/>
                        </svg>
                        <div>
                            <strong>No se pudo guardar la información.</strong>
                            <p>Revisa los campos marcados e inténtalo nuevamente.</p>
                        </div>
                    </div>
                @endif

                <form action="{{ route('proveedores.store') }}" method="POST">
                    @csrf

                    @include('proveedores._form')

                    <p class="form-required-note">Los campos marcados con <span aria-hidden="true">*</span><span class="sr-only">asterisco</span> son obligatorios.</p>

                    <div class="form-actions">
                        <a class="btn btn--ghost" href="{{ route('proveedores.index') }}">Cancelar</a>
                        <button class="btn btn--primary" type="submit">Guardar proveedor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
