<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistema académico para el registro y gestión de ventas de una tienda del sector supermercados.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Inicio') | {{ config('app.name') }}</title>

    <script>
        (function () {
            try {
                var level = localStorage.getItem("tienda-a11y-text");

                if (level !== null && /^-?[0-2]$/.test(level)) {
                    document.documentElement.setAttribute("data-a11y-text", level);
                }

                if (localStorage.getItem("tienda-a11y-contrast") === "1") {
                    document.documentElement.setAttribute("data-a11y-contrast", "high");
                }
            } catch (error) {}
        })();
    </script>

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <a class="skip-link" href="#contenido">Saltar al contenido principal</a>

    <div class="topbar">
        <div class="container topbar__inner">
            <span class="topbar__badge">Proyecto académico</span>
            <p class="topbar__text">Datos ficticios utilizados únicamente con fines educativos.</p>
        </div>
    </div>

    <header class="site-header">
        <div class="container site-header__inner">
            <a class="brand" href="{{ route('home') }}" aria-label="{{ config('app.name') }}, ir al inicio">
                <span class="brand__mark" aria-hidden="true">
                    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 10h18l-2.2 12.5a2 2 0 0 1-2 1.5H11.2a2 2 0 0 1-2-1.5L7 10Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M12 10a4 4 0 0 1 8 0M4 10h24" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="brand__text">
                    <strong>{{ config('app.name') }}</strong>
                    <small>Tienda peruana</small>
                </span>
            </a>

            <div class="header-tools">
                <button
                    class="a11y-toggle"
                    type="button"
                    aria-expanded="false"
                    aria-controls="panel-accesibilidad"
                    aria-haspopup="dialog"
                    aria-label="Abrir panel de accesibilidad"
                    data-a11y-toggle
                >
                    <svg class="btn__icon" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <circle cx="10" cy="10" r="7.5" stroke="currentColor" stroke-width="1.7"/>
                        <path d="M10 6.5v4M10 13.5v.1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    <span>Accesibilidad</span>
                </button>

                <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="main-nav" data-nav-toggle>
                    <span class="nav-toggle__bars" aria-hidden="true">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                    <span class="nav-toggle__label">Menú</span>
                </button>
            </div>

            <nav id="main-nav" class="main-nav" aria-label="Navegación principal" data-nav>
                <ul class="main-nav__list">
                    <li>
                        <a class="main-nav__link {{ request()->routeIs('home') ? 'is-active' : '' }}" href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif>Inicio</a>
                    </li>
                    <li>
                        <a class="main-nav__link {{ request()->routeIs('clientes.*') ? 'is-active' : '' }}" href="{{ route('clientes.index') }}" @if (request()->routeIs('clientes.*')) aria-current="page" @endif>Clientes</a>
                    </li>
                    <li>
                        <a class="main-nav__link {{ request()->routeIs('proveedores.*') ? 'is-active' : '' }}" href="{{ route('proveedores.index') }}" @if (request()->routeIs('proveedores.*')) aria-current="page" @endif>Proveedores</a>
                    </li>
                    <li>
                        <a class="main-nav__link {{ request()->routeIs('productos.*') ? 'is-active' : '' }}" href="{{ route('productos.index') }}" @if (request()->routeIs('productos.*')) aria-current="page" @endif>Productos</a>
                    </li>
                    <li>
                        <a class="main-nav__link {{ request()->routeIs('ventas.*') ? 'is-active' : '' }}" href="{{ route('ventas.index') }}" @if (request()->routeIs('ventas.*')) aria-current="page" @endif>Ventas</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <div
        class="a11y-panel"
        id="panel-accesibilidad"
        role="dialog"
        aria-labelledby="a11y-title"
        data-a11y-panel
        hidden
    >
        <div class="a11y-panel__head">
            <h2 id="a11y-title">Accesibilidad</h2>
            <button class="a11y-icon-button" type="button" data-a11y-close aria-label="Cerrar panel de accesibilidad">
                <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="m6 6 8 8M14 6l-8 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <p class="a11y-panel__intro">Ajusta la visualización del sistema, incluido un modo oscuro de alto contraste. Los cambios se guardan en este navegador.</p>

        <section class="a11y-group" aria-labelledby="a11y-text-title">
            <h3 id="a11y-text-title">Tamaño del texto</h3>
            <div class="a11y-text-controls" role="group" aria-labelledby="a11y-text-title">
                <button class="a11y-button" type="button" data-a11y-text-decrease aria-label="Disminuir tamaño del texto">
                    <span aria-hidden="true">A&minus;</span>
                </button>
                <output class="a11y-value" data-a11y-text-output aria-live="polite">100%</output>
                <button class="a11y-button" type="button" data-a11y-text-increase aria-label="Aumentar tamaño del texto">
                    <span aria-hidden="true">A+</span>
                </button>
            </div>
        </section>

        <section class="a11y-group" aria-labelledby="a11y-contrast-title">
            <h3 id="a11y-contrast-title">Tema visual</h3>
            <button class="a11y-button a11y-button--wide" type="button" data-a11y-contrast-toggle aria-pressed="false">
                Modo oscuro de alto contraste
            </button>
        </section>

        <button class="a11y-button a11y-button--wide" type="button" data-a11y-reset>
            Restaurar valores predeterminados
        </button>

        <section class="a11y-help" aria-labelledby="a11y-help-title">
            <h3 id="a11y-help-title">Uso con teclado</h3>
            <ul>
                <li><kbd>Tab</kbd> y <kbd>Shift</kbd> + <kbd>Tab</kbd> permiten recorrer los controles.</li>
                <li><kbd>Enter</kbd> o <kbd>Espacio</kbd> activan la opción enfocada.</li>
                <li><kbd>Escape</kbd> cierra este panel y devuelve el foco al botón Accesibilidad.</li>
            </ul>
            <p>Los ajustes se guardan únicamente en este navegador.</p>
        </section>

        <p class="sr-only" role="status" aria-live="polite" aria-atomic="true" data-a11y-status></p>
    </div>

    <main id="contenido" tabindex="-1">
        @if (session('success') || session('error'))
            <div class="container flash-container">
                @if (session('success'))
                    <div class="alert alert--success" role="status">
                        <svg class="alert__icon" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="m5 10.5 3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert--error" role="alert">
                        <svg class="alert__icon" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M10 6v5m0 3.2v.1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="10" cy="10" r="7.5" stroke="currentColor" stroke-width="1.7"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container site-footer__inner">
            <div class="site-footer__brand">
                <span class="brand__mark brand__mark--small" aria-hidden="true">
                    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 10h18l-2.2 12.5a2 2 0 0 1-2 1.5H11.2a2 2 0 0 1-2-1.5L7 10Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M12 10a4 4 0 0 1 8 0M4 10h24" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </span>
                <div>
                    <strong>{{ config('app.name') }}</strong>
                    <p>Aplicación web académica para la gestión comercial de una tienda de supermercado.</p>
                </div>
            </div>
            <p class="site-footer__meta">&copy; {{ date('Y') }} &middot; Proyecto académico &middot; Datos ficticios</p>
        </div>
    </footer>

    <script src="{{ asset('js/app.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
