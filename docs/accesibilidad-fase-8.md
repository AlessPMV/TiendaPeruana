# Fase 8: accesibilidad e inclusión digital

**Proyecto:** Sistema de Gestión de Ventas - Tienda Peruana  
**Fecha de verificación:** 2 de noviembre de 2026  
**Referencia:** WCAG 2.2 nivel AA (mejoras seleccionadas)  
**Alcance:** aplicación Laravel existente, sin cambios en la base de datos ni en las reglas de negocio

Este documento describe las mejoras de accesibilidad incorporadas y las comprobaciones realizadas. No constituye el informe académico de la PAU3 ni una certificación de cumplimiento total de WCAG 2.2 AA.

## 1. Funcionalidades implementadas

### 1.1 Panel de accesibilidad

Se agregó un botón visible **Accesibilidad** en el encabezado de todas las páginas. El panel incluye únicamente opciones funcionales:

- Aumentar el tamaño del texto (hasta 120 %).
- Disminuir el tamaño del texto (hasta 85 %).
- Activar y desactivar el modo oscuro de alto contraste.
- Restaurar la configuración visual.
- Instrucciones de navegación con teclado.

El panel se abre con clic, Enter o Espacio, recorre sus controles con Tab y Shift + Tab, se cierra con Escape y devuelve el foco al botón que lo abrió. Las preferencias se guardan en `localStorage` y se aplican antes de pintar la página para evitar parpadeos.

### 1.2 Discapacidad visual

- Contraste revisado en textos, botones, enlaces, formularios, tablas, mensajes y pie de página.
- Escalado global del texto mediante `rem`, sin romper la estructura.
- Modo oscuro de alto contraste con superficies profundas, texto claro y acentos verde menta y ámbar.
- Etiquetas asociadas con `for`, atributos `aria-describedby`, `aria-invalid` y `aria-required` implícito mediante `required`.
- Textos alternativos o `aria-hidden` según corresponda en los SVG decorativos.
- Nombres accesibles en botones y enlaces, especialmente en las acciones de tabla.
- Errores comunicados con texto y no solo con color.
- Indicador de foco visible de 3 px con desplazamiento, también en el modo oscuro.
- Enlaces dentro de párrafos y tablas subrayados para no depender únicamente del color.

### 1.3 Discapacidad auditiva

- Los mensajes de éxito se muestran con `role="status"`.
- Los mensajes de error se muestran con `role="alert"`.
- Las confirmaciones de eliminación son textos visibles en un diálogo nativo.
- No se agregaron sonidos, videos ni contenido multimedia, por lo que no se requieren subtítulos.

### 1.4 Discapacidad motora

- Navegación completa mediante Tab y Shift + Tab.
- Activación de botones y enlaces con Enter o Espacio según el elemento nativo.
- Formularios utilizables sin ratón.
- Áreas interactivas de al menos 24 × 24 px CSS; los controles principales usan 44 px de alto.
- Confirmación previa a la eliminación de clientes, proveedores y productos.
- Al quitar una línea de venta, el foco se mueve a **Agregar producto** para no perder la posición.

### 1.5 Discapacidad cognitiva

- Mensajes en español, directos y sin tecnicismos.
- Instrucciones breves en los formularios y ayudas asociadas a cada campo.
- Campos obligatorios identificados visualmente y anunciados a lectores de pantalla con la palabra «obligatorio».
- Errores junto al campo correspondiente, con explicación para corregirlos.
- Nombres de acciones consistentes en todos los módulos.
- Navegación y estructura uniformes.

## 2. Archivos creados y modificados

### Creados

- `tests/Feature/AccesibilidadTest.php`
- `docs/accesibilidad-fase-8.md`
- `docs/capturas/panel-accesibilidad.png`
- `docs/capturas/panel-alto-contraste.png`
- `docs/capturas/panel-texto-grande.png`
- `docs/capturas/panel-movil.png`
- `docs/capturas/clientes-modo-oscuro.png`
- `docs/capturas/ventas-modo-oscuro.png`

### Modificados

- `resources/views/layouts/app.blade.php`
- `resources/views/clientes/_form.blade.php`
- `resources/views/clientes/create.blade.php`
- `resources/views/clientes/edit.blade.php`
- `resources/views/proveedores/_form.blade.php`
- `resources/views/proveedores/create.blade.php`
- `resources/views/proveedores/edit.blade.php`
- `resources/views/productos/_form.blade.php`
- `resources/views/productos/create.blade.php`
- `resources/views/productos/edit.blade.php`
- `resources/views/ventas/_line.blade.php`
- `resources/views/ventas/create.blade.php`
- `public/css/app.css`
- `public/js/app.js`
- `public/js/ventas.js`
- `README.md`

Las vistas de listado, detalle y el resto de formularios ya contaban con etiquetas, captions, nombres accesibles y mensajes visibles; se mantuvieron y se incluyeron en las pruebas de regresión.

## 3. Pruebas ejecutadas

### 3.1 Pruebas automatizadas

Comando:

```powershell
php artisan test
```

Resultado real obtenido:

| Indicador | Resultado |
|---|---|
| Pruebas ejecutadas | 107 |
| Pruebas aprobadas | 107 |
| Pruebas fallidas | 0 |
| Aserciones | 604 |

La fase agregó `tests/Feature/AccesibilidadTest.php` con 9 pruebas y 232 aserciones. Estas pruebas comprueban:

- Presencia del enlace «Saltar al contenido principal» y del panel en las nueve vistas principales.
- Restauración de preferencias antes de renderizar.
- Etiquetas, obligatoriedad, ayudas y errores descritos por campo.
- Mensajes de éxito y error con `role="status"` y `role="alert"`.
- Nombres accesibles en navegación, tablas y acciones.
- Un solo `h1` por página.
- Anuncios de totales y ayudas en el formulario de ventas.
- Estilos de foco, alto contraste, áreas táctiles y reducción de movimiento.

Comando específico:

```powershell
php artisan test tests/Feature/AccesibilidadTest.php
```

### 3.2 Comprobación funcional en navegador real

Se ejecutó Chrome 157 en modo headless mediante el protocolo DevTools sobre la aplicación servida en `http://127.0.0.1:8010`. Se realizaron 27 comprobaciones del panel y del teclado, más una revisión del tema oscuro en 11 páginas. Todas finalizaron correctamente:

| Grupo | Comprobaciones |
|---|---|
| Estructura | Idioma `es`, un solo `h1`, enlace de salto y destino enfocable |
| Formularios | 6 campos con etiqueta accesible, 3 obligatorios, nota con «asterisco» |
| Nombres | 0 controles sin nombre accesible |
| Navegación | Navegación principal y ruta de navegación etiquetadas |
| Panel | Inicia oculto, se abre, informa el estado y enfoca el botón cerrar |
| Teclado | 5 controles enfocables; Tab y Shift + Tab permanecen en el panel; Escape cierra y devuelve el foco |
| Texto | Nivel 2 aplicado, 19.2 px efectivos, valor 120 %, preferencia guardada y anuncio |
| Modo oscuro | Fondo `rgb(11, 20, 17)`, texto `rgb(243, 247, 245)`, `color-scheme: dark`, estado `aria-pressed` y preferencia guardada |
| Restaurar | Elimina tamaño, contraste y claves de almacenamiento |
| Páginas | 11 rutas con modo oscuro, un solo `h1`, sin desborde horizontal y sin textos bajo el umbral |

Resultado: **27 comprobaciones del panel con 0 fallas y 11 páginas en modo oscuro sin observaciones**.

### 3.3 Contraste calculado

Se aplicó la fórmula de luminancia relativa de WCAG sobre los colores usados en la interfaz:

| Elemento | Texto | Fondo | Relación | Resultado |
|---|---|---|---|---|
| Texto principal | `#172a22` | `#f5f8f5` | 14.11:1 | AA normal |
| Texto secundario | `#5b6f66` | `#ffffff` | 5.37:1 | AA normal |
| Texto de ayuda | `#5f7268` | `#ffffff` | 5.13:1 | AA normal |
| Encabezado de marca | `#0a5c3a` | `#ffffff` | 8.06:1 | AA normal |
| Texto de botón primario | `#ffffff` | `#0f7a4d` | 5.37:1 | AA normal |
| Enlace en tabla | `#0a5c3a` | `#ffffff` | 8.06:1 | AA normal |
| Error de campo | `#b42318` | `#ffffff` | 6.57:1 | AA normal |
| Alerta de error | `#a52218` | `#fff1f0` | 6.72:1 | AA normal |
| Alerta de éxito | `#0a5c3a` | `#e8f7ee` | 7.28:1 | AA normal |
| Indicador de foco | `#0b4ea2` | `#ffffff` | 8.00:1 | AA normal |
| Pie de página | `#8fb3a0` | `#0e241b` | 7.09:1 | AA normal |
| Modo oscuro: texto principal | `#f3f7f5` | `#0b1411` | 17.31:1 | AAA |
| Modo oscuro: texto secundario | `#c7d6cf` | `#0b1411` | 12.43:1 | AAA |
| Modo oscuro: texto sobre tarjeta | `#f3f7f5` | `#14241e` | 14.94:1 | AAA |
| Modo oscuro: texto atenuado | `#c7d6cf` | `#0f1d17` | 11.54:1 | AAA |
| Modo oscuro: botón primario | `#07130e` | `#7ff0c0` | 13.65:1 | AAA |
| Modo oscuro: enlace activo | `#b9f7dc` | `#123b2c` | 10.31:1 | AAA |
| Modo oscuro: foco | `#ffd400` | `#0b1411` | 13.07:1 | AAA |
| Modo oscuro: error de campo | `#ff9e94` | `#14241e` | 8.13:1 | AAA |
| Modo oscuro: alerta de éxito | `#c9f8df` | `#103a29` | 10.83:1 | AAA |
| Modo oscuro: alerta de error | `#ffc9c4` | `#3a1715` | 10.97:1 | AAA |
| Modo oscuro: acento | `#ffd166` | `#3a2d10` | 9.32:1 | AAA |

### 3.4 Pruebas manuales y de marcado

| Prueba | Método | Resultado |
|---|---|---|
| Navegación por teclado | Simulación de Tab, Shift + Tab y Escape en Chrome | Correcto |
| Tamaño del texto | Activación hasta 120 % y verificación de tamaño efectivo | Correcto |
| Modo oscuro de alto contraste | Activación y verificación de colores calculados en 11 páginas | Correcto |
| Legibilidad del modo oscuro | Revisión de 128 textos visibles | 0 por debajo del umbral |
| Lectores de pantalla | Revisión de etiquetas, nombres accesibles, roles y encabezados | Sin observaciones en el marcado |
| Mensajes visuales | Pruebas HTTP de éxito, error y confirmación | Correcto |
| Formularios accesibles | Etiquetas, ayudas, errores y recorrido con teclado | Correcto |
| Diseño adaptable | Captura en 1280 px y 390 px; panel accesible en móvil | Correcto |

Las capturas del panel se encuentran en `docs/capturas/`:

- `panel-accesibilidad.png`: panel abierto en escritorio.
- `panel-alto-contraste.png`: panel con el modo oscuro de alto contraste.
- `panel-texto-grande.png`: panel con texto al 120 %.
- `panel-movil.png`: panel en una pantalla de 390 px de ancho.
- `clientes-modo-oscuro.png`: listado de clientes demo en modo oscuro.
- `ventas-modo-oscuro.png`: listado de ventas demo en modo oscuro.

## 4. Problemas encontrados y correcciones

| Problema | Corrección |
|---|---|
| El foco podía salir del panel al recorrerlo con Tab | Se agregó un ciclo de foco dentro del panel con Tab y Shift + Tab |
| La obligatoriedad se indicaba con un asterisco oculto para lectores de pantalla | Se agregó el texto accesible «(obligatorio)» en cada etiqueta y «asterisco» en la nota |
| El enlace de salto no siempre movía el foco al contenido | Se agregó `tabindex="-1"` al elemento `main` |
| Una etiqueta de estado usaba un gris con contraste insuficiente (3.78:1) | Se reemplazó por el color secundario con contraste 4.77:1 sobre su fondo |
| Los enlaces dentro de textos se distinguían solo por color | Se agregó subrayado permanente |
| Al quitar una línea de venta se perdía el foco del teclado | El foco pasa a **Agregar producto** |
| No se respetaba la preferencia de movimiento reducido | Se agregó `@media (prefers-reduced-motion: reduce)` |
| El panel no explicaba su funcionamiento | Se agregó un texto introductorio breve |
| El alto contraste usaba negro, blanco y amarillo puros, con una apariencia agresiva | Se reemplazó por un tema oscuro con superficies, acentos y contraste verificado |

No se modificaron las reglas de negocio, las rutas, los modelos, las migraciones ni los datos. Las 97 pruebas existentes siguen aprobadas.

## 5. Criterios de aceptación

| Criterio | Estado |
|---|---|
| Panel de accesibilidad funcional | Cumplido |
| Aumentar y disminuir el tamaño del texto | Cumplido |
| Activar y desactivar el modo oscuro de alto contraste | Cumplido |
| Navegación mediante teclado | Cumplido |
| Nombres accesibles en elementos interactivos | Cumplido |
| Etiquetas e instrucciones comprensibles en formularios | Cumplido |
| Mensajes visibles sin depender del sonido | Cumplido |
| Mejoras aplicadas a todos los módulos | Cumplido |
| Funciones originales sin regresiones | Cumplido (107 pruebas) |
| Pruebas de accesibilidad ejecutadas y documentadas | Cumplido |

## 6. Limitaciones

- No se afirma cumplimiento total de WCAG 2.2 AA; se aplicaron mejoras concretas y se documentaron las comprobaciones realizadas.
- No se dispuso de NVDA, JAWS ni VoiceOver para una prueba auditiva real. La revisión se basó en el marcado accesible y en las propiedades expuestas por Chrome.
- No se ejecutaron axe DevTools, WAVE ni Lighthouse en este entorno. El contraste se calculó con la fórmula oficial de WCAG y las comprobaciones de teclado se hicieron en Chrome headless.
- El panel requiere JavaScript. Si el navegador lo desactiva, las preferencias visuales no pueden cambiarse.
- Las preferencias se guardan solo en el navegador utilizado; no se sincronizan entre equipos ni sesiones.
- Las confirmaciones de eliminación usan el diálogo nativo del navegador. Es accesible por teclado, pero su apariencia depende del navegador.
- Las capturas fueron generadas automáticamente y sirven como evidencia del estado del panel; no sustituyen una evaluación manual con lector de pantalla.
- No se realizaron pruebas con usuarios reales con discapacidad.

## 7. Comandos de reproducción

```powershell
# Suite completa
php artisan test

# Solo pruebas de accesibilidad
php artisan test tests/Feature/AccesibilidadTest.php

# Comprobar sintaxis del JavaScript
node --check public/js/app.js
node --check public/js/ventas.js

# Servidor local
php artisan serve
```
