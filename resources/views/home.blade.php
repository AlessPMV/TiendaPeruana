@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <section class="hero">
        <div class="container hero__inner">
            <div class="hero__content">
                <span class="eyebrow">
                    <span class="eyebrow__dot" aria-hidden="true"></span>
                    Gestión comercial para supermercados
                </span>
                <h1>Todo tu registro de ventas, <span class="highlight">en un solo lugar.</span></h1>
                <p class="hero__lead">
                    Una aplicación web para organizar clientes, proveedores, productos y ventas
                    de una tienda del sector supermercados, con una interfaz clara y datos validados.
                </p>
                <div class="hero__actions">
                    <a class="btn btn--primary" href="#modulos">
                        Explorar módulos
                        <svg class="btn__icon" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M4 10h12m0 0-4.5-4.5M16 10l-4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <a class="btn btn--ghost" href="#categorias">Ver categorías</a>
                </div>
                <ul class="hero__checks">
                    <li>
                        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m5 10.5 3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Validación de datos
                    </li>
                    <li>
                        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m5 10.5 3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Diseño adaptable
                    </li>
                    <li>
                        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m5 10.5 3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Datos de prueba
                    </li>
                </ul>
            </div>

            <div class="hero__visual">
                <div class="summary-card">
                    <div class="summary-card__header">
                        <div>
                            <span class="summary-card__label">Panel de la tienda</span>
                            <strong>Resumen general</strong>
                        </div>
                        <span class="status-pill">En preparación</span>
                    </div>

                    <ul class="summary-list">
                        <li class="summary-list__item">
                            <span class="summary-list__icon summary-list__icon--green" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.4" stroke="currentColor" stroke-width="1.8"/><path d="M5 19c.7-3.2 3.4-5 7-5s6.3 1.8 7 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            </span>
                            <span class="summary-list__text"><strong>Clientes</strong><small>Registro y consulta</small></span>
                            <span class="summary-list__state summary-list__state--available">Disponible</span>
                        </li>
                        <li class="summary-list__item">
                            <span class="summary-list__icon summary-list__icon--amber" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none"><path d="M4 9h16l-1.4 9.2a2 2 0 0 1-2 1.8H7.4a2 2 0 0 1-2-1.8L4 9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M8 9V7a4 4 0 0 1 8 0v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            </span>
                            <span class="summary-list__text"><strong>Proveedores</strong><small>Datos de contacto</small></span>
                            <span class="summary-list__state summary-list__state--available">Disponible</span>
                        </li>
                        <li class="summary-list__item">
                            <span class="summary-list__icon summary-list__icon--blue" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m4 7.5 8 4.5 8-4.5M12 12v9" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                            </span>
                            <span class="summary-list__text"><strong>Productos</strong><small>Catálogo por categoría</small></span>
                            <span class="summary-list__state summary-list__state--available">Disponible</span>
                        </li>
                        <li class="summary-list__item">
                            <span class="summary-list__icon summary-list__icon--rose" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 8h6M9 12h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            </span>
                            <span class="summary-list__text"><strong>Ventas</strong><small>Registro de operaciones</small></span>
                            <span class="summary-list__state summary-list__state--available">Disponible</span>
                        </li>
                    </ul>

                    <div class="summary-card__footer">
                        <span>Categorías contempladas</span>
                        <div class="chip-row chip-row--compact">
                            <span class="chip">Abarrotes</span>
                            <span class="chip">Bebidas</span>
                            <span class="chip">Lácteos</span>
                            <span class="chip">Limpieza</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="base-strip" aria-label="Base técnica preparada">
        <div class="container base-strip__inner">
            <span class="base-strip__title">Base del proyecto preparada</span>
            <ul class="base-strip__items">
                <li><span class="base-strip__check" aria-hidden="true"></span> Laravel</li>
                <li><span class="base-strip__check" aria-hidden="true"></span> MySQL</li>
                <li><span class="base-strip__check" aria-hidden="true"></span> Blade</li>
                <li><span class="base-strip__check" aria-hidden="true"></span> PHPUnit</li>
            </ul>
        </div>
    </section>

    <section id="modulos" class="section">
        <div class="container">
            <div class="section__heading">
                <span class="eyebrow">Módulos del sistema</span>
                <h2>Una base lista para crecer</h2>
                <p>Los módulos se incorporarán de forma progresiva, manteniendo la misma estructura visual y las validaciones correspondientes.</p>
            </div>

            <div class="module-grid">
                <article class="module-card">
                    <span class="module-card__icon module-card__icon--green" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.4" stroke="currentColor" stroke-width="1.8"/><path d="M5 19c.7-3.2 3.4-5 7-5s6.3 1.8 7 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                    <h3>Clientes</h3>
                    <p>Administración de los datos de los clientes que realizan compras en la tienda.</p>
                    <div class="module-card__footer module-card__footer--split">
                        <span class="badge badge--available">Disponible</span>
                        <a class="module-card__link" href="{{ route('clientes.index') }}">Abrir módulo</a>
                    </div>
                </article>

                <article class="module-card">
                    <span class="module-card__icon module-card__icon--amber" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none"><path d="M4 9h16l-1.4 9.2a2 2 0 0 1-2 1.8H7.4a2 2 0 0 1-2-1.8L4 9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M8 9V7a4 4 0 0 1 8 0v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                    <h3>Proveedores</h3>
                    <p>Registro de las empresas o personas que abastecen los productos de la tienda.</p>
                    <div class="module-card__footer module-card__footer--split">
                        <span class="badge badge--available">Disponible</span>
                        <a class="module-card__link" href="{{ route('proveedores.index') }}">Abrir módulo</a>
                    </div>
                </article>

                <article class="module-card">
                    <span class="module-card__icon module-card__icon--blue" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m4 7.5 8 4.5 8-4.5M12 12v9" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                    </span>
                    <h3>Productos</h3>
                    <p>Catálogo de productos organizado por categorías, con precios y datos comerciales.</p>
                    <div class="module-card__footer module-card__footer--split">
                        <span class="badge badge--available">Disponible</span>
                        <a class="module-card__link" href="{{ route('productos.index') }}">Abrir módulo</a>
                    </div>
                </article>

                <article class="module-card">
                    <span class="module-card__icon module-card__icon--rose" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 8h6M9 12h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                    <h3>Ventas</h3>
                    <p>Registro de las ventas realizadas, con sus productos, cantidades y totales.</p>
                    <div class="module-card__footer module-card__footer--split">
                        <span class="badge badge--available">Disponible</span>
                        <a class="module-card__link" href="{{ route('ventas.index') }}">Abrir módulo</a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section id="categorias" class="section section--tinted">
        <div class="container">
            <div class="section__heading section__heading--center">
                <span class="eyebrow">Contexto comercial</span>
                <h2>Categorías de ejemplo</h2>
                <p>El catálogo contempla las categorías habituales de una tienda de supermercado. Todos los registros serán ficticios.</p>
            </div>

            <ul class="category-grid">
                <li class="category-item"><span class="category-item__dot category-item__dot--green" aria-hidden="true"></span>Abarrotes</li>
                <li class="category-item"><span class="category-item__dot category-item__dot--blue" aria-hidden="true"></span>Bebidas</li>
                <li class="category-item"><span class="category-item__dot category-item__dot--amber" aria-hidden="true"></span>Lácteos</li>
                <li class="category-item"><span class="category-item__dot category-item__dot--teal" aria-hidden="true"></span>Limpieza</li>
                <li class="category-item"><span class="category-item__dot category-item__dot--rose" aria-hidden="true"></span>Cuidado personal</li>
                <li class="category-item"><span class="category-item__dot category-item__dot--lime" aria-hidden="true"></span>Frutas y verduras</li>
                <li class="category-item"><span class="category-item__dot category-item__dot--orange" aria-hidden="true"></span>Snacks</li>
                <li class="category-item"><span class="category-item__dot category-item__dot--sky" aria-hidden="true"></span>Productos congelados</li>
            </ul>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="next-step">
                <div>
                    <span class="eyebrow">Siguiente etapa</span>
                    <h2>La estructura está lista para incorporar los módulos de gestión.</h2>
                    <p>La navegación, el diseño base y la conexión con la base de datos quedaron preparados para continuar con el desarrollo por fases.</p>
                </div>
                <a class="btn btn--primary" href="#modulos">Revisar módulos</a>
            </div>
        </div>
    </section>
@endsection
