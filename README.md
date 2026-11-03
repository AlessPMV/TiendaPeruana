# Sistema de Gestión de Ventas - Tienda Peruana

Aplicación web académica desarrollada con Laravel para administrar clientes, proveedores, productos y ventas de una tienda del sector supermercados. Todos los datos utilizados son ficticios y tienen fines educativos.

## Funcionalidades

- Página de inicio con acceso a los módulos del sistema.
- CRUD de clientes con validación de documento único, correo y teléfono.
- CRUD de proveedores con validación de RUC único y datos de contacto opcionales.
- CRUD de productos con categoría, precio, stock y proveedor asociado.
- Registro de ventas con múltiples productos, cálculo de subtotales y total en el servidor.
- Descuento automático de stock dentro de una transacción.
- Consulta del listado y detalle de ventas.
- Bloqueo de eliminación de registros con relaciones activas.
- Datos demo: 3 proveedores, 8 productos, 6 clientes y 8 ventas con sus detalles.
- Panel de accesibilidad con escala de texto, modo oscuro de alto contraste y restauración de preferencias.
- Navegación por teclado, enlace para saltar al contenido y mensajes accesibles.

## Tecnologías

- PHP 8.2
- Laravel 12
- MySQL / MariaDB
- Blade, CSS y JavaScript sin frameworks de interfaz
- PHPUnit 11
- Xdebug para medición de cobertura

## Requisitos

- PHP 8.2 o superior con las extensiones requeridas por Laravel.
- Composer.
- MySQL o MariaDB.
- XAMPP, Laragon u otro entorno equivalente.
- Xdebug si se desea generar el reporte de cobertura.

## Instalación

1. Instalar las dependencias de PHP:

```powershell
composer install
```

2. Crear el archivo de configuración si no existe:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

3. Crear la base de datos en MySQL:

```sql
CREATE DATABASE tienda_peruana
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

4. Configurar la conexión en `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tienda_peruana
DB_USERNAME=root
DB_PASSWORD=
```

5. Ejecutar las migraciones y cargar los datos demo (proveedores, productos, clientes y ventas):

```powershell
php artisan migrate --seed
```

6. Iniciar el servidor local:

```powershell
php artisan serve
```

La aplicación estará disponible en `http://127.0.0.1:8000`.

## Uso

1. Registrar al menos un cliente en el módulo **Clientes**.
2. Verificar que existan proveedores y productos en el catálogo.
3. Abrir **Ventas > Registrar venta**.
4. Seleccionar el cliente, agregar uno o más productos y confirmar la operación.
5. Revisar el total y el detalle en la página de la venta.

El precio unitario y el total siempre se calculan en el servidor. Si el stock es insuficiente, la venta se rechaza y no se modifican datos.

Después de ejecutar `php artisan migrate --seed`, el sistema incluye 6 clientes y 8 ventas de ejemplo ya registradas. Los seeders de catálogo y de clientes/ventas son idempotentes: se pueden volver a ejecutar sin duplicar esos registros.

## Accesibilidad

Todas las páginas incluyen un botón **Accesibilidad** con estas opciones:

- Aumentar o disminuir el tamaño del texto hasta un 120 % y un 85 %.
- Activar o desactivar el modo oscuro de alto contraste.
- Restaurar la configuración visual.

El panel se controla con el teclado: se abre con Enter o Espacio, mantiene el foco con Tab y Shift + Tab, se cierra con Escape y devuelve el foco al botón de origen. Las preferencias se guardan en `localStorage`.

La interfaz incorpora etiquetas asociadas, textos de ayuda, obligatoriedad anunciada, errores visibles, nombres accesibles en tablas y botones, foco visible, enlaces subrayados, `role="status"` y `role="alert"` para mensajes, y soporte para `prefers-reduced-motion`. Las mejoras se aplicaron como referencia a WCAG 2.2 AA; no se declara cumplimiento total.

El detalle de las mejoras, las pruebas y las limitaciones está en [docs/accesibilidad-fase-8.md](docs/accesibilidad-fase-8.md).

## Pruebas

Ejecutar toda la suite:

```powershell
php artisan test
```

Ejecutar por tipo de prueba:

```powershell
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
php artisan test tests/Feature/CajaBlancaTest.php
```

Generar cobertura con Xdebug:

```powershell
$env:XDEBUG_MODE='coverage'
php artisan test --coverage
```

### Organización de las pruebas

- **Caja negra:** pruebas funcionales de los CRUD y del flujo de ventas en `tests/Feature/ClienteCrudTest.php`, `ProveedorCrudTest.php`, `ProductoCrudTest.php`, `VentaCrudTest.php`, `HomePageTest.php` y `DatabaseRelationshipsTest.php`.
- **Caja blanca:** pruebas de ramas de controladores, errores de base de datos, stock insuficiente y transacciones en `tests/Feature/CajaBlancaTest.php`.
- **Unitarias:** pruebas del cálculo de ventas y de las reglas de validación en `tests/Unit/CalculadoraVentaTest.php` y `tests/Unit/ValidacionReglasTest.php`.
- **Accesibilidad:** pruebas de panel, teclado, etiquetas, mensajes y estructura en `tests/Feature/AccesibilidadTest.php`.
- **Datos demo:** comprobación de carga, totales e idempotencia en `tests/Feature/DatosDemoTest.php`.

La suite completa suma 107 pruebas y 604 aserciones. El resultado de las fases 1 a 7 se encuentra en [docs/verificacion-tecnica.md](docs/verificacion-tecnica.md), el de la fase 8 en [docs/accesibilidad-fase-8.md](docs/accesibilidad-fase-8.md) y el inventario de casos con sus resultados en [docs/registro-pruebas.md](docs/registro-pruebas.md).

## Estructura principal

```text
app/
  Http/Controllers/    Controladores de clientes, proveedores, productos y ventas
  Http/Requests/       Validaciones de formularios
  Models/              Modelos Eloquent y relaciones
  Services/            CalculadoraVenta
database/
  migrations/          Esquema de la base de datos
  seeders/             Catálogo de demostración
resources/views/       Vistas Blade por módulo
public/js/             JavaScript del menú, panel de accesibilidad y formulario de ventas
tests/
  Feature/             Pruebas funcionales, de caja blanca y de accesibilidad
  Unit/                Pruebas unitarias
docs/                  Informes de verificación y capturas de accesibilidad
```

## Reglas de negocio

- Un cliente con ventas registradas no puede eliminarse.
- Un proveedor con productos asociados no puede eliminarse.
- Un producto usado en una venta no puede eliminarse.
- Una venta no puede editarse ni eliminarse desde la interfaz.
- No se permite repetir el mismo producto en una venta.
- La cantidad debe ser un entero mayor que cero.
- El stock se descuenta únicamente cuando la venta se registra correctamente.

## Notas

- Este proyecto no incluye autenticación ni roles, ya que no forman parte del alcance solicitado.
- El servidor de desarrollo de Laravel es adecuado para pruebas locales, no para producción.
- El informe académico PAU3 se entrega por separado; este repositorio contiene la aplicación y su verificación técnica.
