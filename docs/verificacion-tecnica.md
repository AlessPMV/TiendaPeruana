# Informe técnico de verificación

**Proyecto:** Sistema de Gestión de Ventas - Tienda Peruana  
**Fecha de verificación:** 2 de noviembre de 2026  
**Alcance:** aplicación Laravel, base de datos MySQL y suite automatizada de pruebas  
**Fuera de alcance:** redacción del informe académico PAU3, autenticación, roles, despliegue en producción y pruebas de carga

> **Nota:** este informe cubre las fases 1 a 7. Las mejoras y pruebas de accesibilidad de la fase 8 se documentan en [accesibilidad-fase-8.md](accesibilidad-fase-8.md). Con esa incorporación y las pruebas de datos demo, la suite completa suma 107 pruebas y 604 aserciones.

## 1. Objetivo

Verificar el funcionamiento de la aplicación mediante pruebas de caja negra, caja blanca y unitarias, corrigiendo los defectos encontrados y documentando la evidencia obtenida. La verificación cubre los módulos de clientes, proveedores, productos y ventas, además de las reglas de integridad y el cálculo de importes.

## 2. Entorno de verificación

| Elemento | Versión o configuración |
|---|---|
| Sistema operativo | Windows, entorno XAMPP |
| PHP | 8.2.12 |
| Laravel | 12.70.2 |
| Composer | 2.10.3 |
| Base de datos real | MySQL/MariaDB de XAMPP, base `tienda_peruana` |
| Base de datos de pruebas | SQLite en memoria mediante `phpunit.xml` |
| Framework de pruebas | PHPUnit 11.5.57 |
| Cobertura | Xdebug 3.5.3 |
| Zona horaria | `America/Lima` |
| Idioma de la aplicación | Español |

La suite automatizada se ejecuta sobre SQLite para aislar cada prueba. La verificación integral del flujo HTTP y de la persistencia se realizó sobre MySQL con el servidor real de la aplicación.

## 3. Clasificación de las pruebas

| Tipo | Propósito | Archivos principales |
|---|---|---|
| Caja negra | Validar comportamiento observable desde la interfaz y las rutas HTTP, sin depender de la implementación interna | `tests/Feature/ClienteCrudTest.php`, `ProveedorCrudTest.php`, `ProductoCrudTest.php`, `VentaCrudTest.php`, `HomePageTest.php`, `DatabaseRelationshipsTest.php` |
| Caja blanca | Ejercitar ramas internas de controladores, manejo de errores, transacciones y caminos defensivos | `tests/Feature/CajaBlancaTest.php` |
| Unitarias | Verificar cálculos y reglas de validación de forma aislada | `tests/Unit/CalculadoraVentaTest.php`, `tests/Unit/ValidacionReglasTest.php` |

## 4. Resultado global

| Indicador | Resultado |
|---|---|
| Pruebas ejecutadas | 97 |
| Pruebas aprobadas | 97 |
| Pruebas fallidas | 0 |
| Aserciones | 348 |
| Cobertura de líneas reportada por PHPUnit | 98.9 % |
| Controladores, requests, modelos utilizados y `CalculadoraVenta` | 100 % |
| Rutas registradas | 23 |
| Rutas con cobertura funcional | 23 |

El total de 97 pruebas incluye dos pruebas base generadas por Laravel (`ExampleTest`). Las 95 restantes corresponden a comportamiento del sistema.

## 5. Pruebas de caja negra

Los casos se ejecutaron a través de rutas HTTP y formularios, verificando códigos de respuesta, persistencia, mensajes y efectos sobre el stock.

### 5.1 Inicio e integridad

| Código | Caso | Datos o pasos | Resultado esperado | Obtenido |
|---|---|---|---|---|
| CN-01 | Página de inicio | GET `/` | Estado 200, identidad del sistema, módulos y categorías visibles | Aprobado |
| CN-02 | Relaciones de modelos | Crear cliente, proveedor, producto, venta y detalle | Relaciones Eloquent disponibles y coherentes | Aprobado |
| CN-03 | Eliminación en cascada | Eliminar una venta con detalle | Se eliminan la venta y sus detalles | Aprobado |

### 5.2 Clientes

| Código | Caso | Datos o pasos | Resultado esperado | Obtenido |
|---|---|---|---|---|
| CN-04 | Listado de clientes | GET `/clientes` con un cliente registrado | El cliente aparece en la tabla | Aprobado |
| CN-05 | Registro válido | Nombres, apellidos, documento único y datos opcionales | Cliente persistido y mensaje de éxito | Aprobado |
| CN-06 | Contacto opcional | Registrar cliente sin teléfono, correo ni dirección | Cliente persistido con campos opcionales nulos | Aprobado |
| CN-07 | Campos obligatorios | Enviar nombres, apellidos y documento vacíos | Errores de validación y sin registro | Aprobado |
| CN-08 | Documento duplicado | Registrar dos clientes con el mismo documento | Rechazo con mensaje de documento existente | Aprobado |
| CN-09 | Correo y teléfono inválidos | Correo sin formato válido y teléfono demasiado corto | Mensajes de validación específicos | Aprobado |
| CN-10 | Edición y actualización | Abrir formulario, modificar datos y guardar | Datos actualizados y mensaje de éxito | Aprobado |
| CN-11 | Eliminación permitida | Cliente sin ventas, DELETE | Cliente eliminado | Aprobado |
| CN-12 | Eliminación bloqueada | Cliente con venta asociada, DELETE | Rechazo y cliente conservado | Aprobado |

### 5.3 Proveedores

| Código | Caso | Datos o pasos | Resultado esperado | Obtenido |
|---|---|---|---|---|
| CN-13 | Listado de proveedores | GET `/proveedores` | Proveedores registrados visibles | Aprobado |
| CN-14 | Registro válido | Nombre, RUC único y contacto | Proveedor persistido | Aprobado |
| CN-15 | Contacto opcional | Sin teléfono, correo ni dirección | Proveedor persistido con campos opcionales nulos | Aprobado |
| CN-16 | Campos obligatorios | Nombre y RUC vacíos | Errores de validación | Aprobado |
| CN-17 | RUC duplicado | Repetir un RUC existente | Rechazo con mensaje de RUC existente | Aprobado |
| CN-18 | Correo y teléfono inválidos | Formatos incorrectos | Mensajes de validación | Aprobado |
| CN-19 | Edición y actualización | Modificar datos y guardar | Datos actualizados | Aprobado |
| CN-20 | Eliminación permitida | Proveedor sin productos | Proveedor eliminado | Aprobado |
| CN-21 | Eliminación bloqueada | Proveedor con productos | Rechazo y proveedor conservado | Aprobado |

### 5.4 Productos

| Código | Caso | Datos o pasos | Resultado esperado | Obtenido |
|---|---|---|---|---|
| CN-22 | Listado de productos | GET `/productos` | Productos y proveedor visibles | Aprobado |
| CN-23 | Registro válido | Nombre, categoría, precio, stock y proveedor existente | Producto persistido | Aprobado |
| CN-24 | Descripción opcional | Registrar producto sin descripción | Producto persistido | Aprobado |
| CN-25 | Campos obligatorios | Nombre, categoría, precio, stock o proveedor ausentes | Errores de validación | Aprobado |
| CN-26 | Precio negativo | Precio menor que cero | Rechazo | Aprobado |
| CN-27 | Precio cero | Precio igual a cero | Rechazo con mensaje específico | Aprobado |
| CN-28 | Stock negativo | Stock menor que cero | Rechazo con mensaje específico | Aprobado |
| CN-29 | Proveedor inexistente | Identificador de proveedor no registrado | Rechazo con mensaje de proveedor inexistente | Aprobado |
| CN-30 | Edición y actualización | Modificar datos y guardar | Producto actualizado | Aprobado |
| CN-31 | Eliminación permitida | Producto sin detalles de venta | Producto eliminado | Aprobado |
| CN-32 | Eliminación bloqueada | Producto incluido en una venta | Rechazo y producto conservado | Aprobado |

### 5.5 Ventas

| Código | Caso | Datos o pasos | Resultado esperado | Obtenido |
|---|---|---|---|---|
| CN-33 | Listado de ventas | GET `/ventas` con venta registrada | Venta, cliente, fecha, cantidad de productos y total visibles | Aprobado |
| CN-34 | Venta válida | Cliente, producto y cantidad; precios enviados alterados desde el cliente | El servidor ignora precios del cliente y calcula subtotal y total | Aprobado |
| CN-35 | Venta sin cliente | Enviar formulario sin `cliente_id` | Error y sin venta | Aprobado |
| CN-36 | Cliente inexistente | Enviar `cliente_id` no registrado | Error y sin venta | Aprobado |
| CN-37 | Venta sin productos | Enviar arreglo `productos` vacío | Error y sin venta | Aprobado |
| CN-38 | Cantidad cero | Cantidad igual a cero | Error y sin venta | Aprobado |
| CN-39 | Cantidad negativa | Cantidad menor que cero | Error y sin venta | Aprobado |
| CN-40 | Producto inexistente | Identificador de producto no registrado | Error y sin venta | Aprobado |
| CN-41 | Stock insuficiente | Cantidad mayor que el stock disponible | Error, sin venta, sin detalle y stock sin cambios | Aprobado |
| CN-42 | Detalle de venta | GET `/ventas/{id}` | Cliente, productos, cantidades, precios, subtotales y total visibles | Aprobado |

## 6. Pruebas de caja blanca

| Código | Rama o camino ejercitado | Resultado esperado | Obtenido |
|---|---|---|---|
| CB-01 | Formularios de creación de los cuatro módulos | Estado 200 y formulario renderizado | Aprobado |
| CB-02 | Eliminación de producto con detalles | Rama de bloqueo, mensaje de error y registro conservado | Aprobado |
| CB-03 | Eliminación de producto sin detalles | Rama de eliminación y mensaje de éxito | Aprobado |
| CB-04 | Eliminación de proveedor con productos | Rama de bloqueo y registro conservado | Aprobado |
| CB-05 | Eliminación de proveedor sin productos | Rama de eliminación | Aprobado |
| CB-06 | Eliminación de cliente con ventas | Rama de bloqueo y registro conservado | Aprobado |
| CB-07 | Eliminación de cliente sin ventas | Rama de eliminación | Aprobado |
| CB-08 | Excepción de base de datos al eliminar | Captura de `QueryException` y mensaje de registros relacionados | Aprobado |
| CB-09 | Producto inexistente dentro de la transacción | Rama defensiva, `ValidationException` y sin venta | Aprobado |
| CB-10 | Stock insuficiente dentro de la transacción | Excepción de validación, rollback, sin venta ni detalle y stock conservado | Aprobado |
| CB-11 | Venta exitosa | Persistencia de venta y detalle, total correcto y descuento de stock | Aprobado |
| CB-12 | Cálculo delegado a `CalculadoraVenta` | Normalización de precio, subtotal y total dentro de la transacción | Aprobado |

La prueba CB-11 detectó un defecto en el refactor: la clausura de `DB::transaction` no recibía `$calculadora`, lo que producía un error 500 en una venta válida. Se corrigió la captura de la variable y la prueba quedó aprobada.

## 7. Pruebas unitarias

| Código | Caso | Resultado esperado | Obtenido |
|---|---|---|---|
| UN-01 | Normalización de precio | Valor numérico a dos decimales | Aprobado |
| UN-02 | Subtotal de dos unidades | `24.90 × 2 = 49.80` | Aprobado |
| UN-03 | Subtotal con precio decimal | `4.20 × 3 = 12.60` | Aprobado |
| UN-04 | Total de varios subtotales | `49.80 + 12.60 = 62.40` | Aprobado |
| UN-05 | Total sin errores de redondeo | `0.10 + 0.10 + 0.10 = 0.30` | Aprobado |
| UN-06 | Total sin subtotales | Resultado `0.0` | Aprobado |
| UN-07 | Cantidad cero o negativa | `InvalidArgumentException` | Aprobado |
| UN-08 | Precio cero, negativo o no numérico | `InvalidArgumentException` | Aprobado |
| UN-09 | Subtotal negativo en el total | `InvalidArgumentException` | Aprobado |
| UN-10 | Reglas de cliente | Datos válidos aceptados; campos obligatorios, correo y teléfono inválidos rechazados | Aprobado |
| UN-11 | Reglas de proveedor | Datos válidos aceptados; RUC duplicado rechazado | Aprobado |
| UN-12 | Reglas de producto | Precio cero/negativo, stock negativo y proveedor inexistente rechazados | Aprobado |
| UN-13 | Reglas de venta | Cliente, productos, cantidades, producto inexistente y producto repetido validados | Aprobado |

## 8. Cobertura

Comando ejecutado:

```powershell
$env:XDEBUG_MODE='coverage'
php artisan test --coverage
```

| Componente | Cobertura de líneas |
|---|---|
| `ClienteController` | 100 % |
| `ProductoController` | 100 % |
| `ProveedorController` | 100 % |
| `VentaController` | 100 % |
| Requests de clientes, proveedores, productos y ventas | 100 % |
| Modelos utilizados | 100 % |
| `CalculadoraVenta` | 100 % |
| `Models\User` | 0 % |
| **Total reportado** | **98.9 %** |

`Models\User` no se utiliza en la aplicación porque no se solicitó autenticación. La cobertura corresponde a archivos PHP medidas por PHPUnit; no incluye vistas Blade, CSS ni JavaScript.

## 9. Verificación integral con MySQL

Se inició el servidor real de Laravel y se ejecutó un flujo HTTP completo contra MySQL:

1. Verificación de 9 páginas: inicio, listados y formularios de creación de clientes, proveedores, productos y ventas.
2. Verificación de 4 recursos estáticos: `app.css`, `app.js`, `ventas.js` y `favicon.svg`.
3. Creación de un cliente temporal mediante el formulario real.
4. Registro de una venta de 2 unidades del producto con precio `S/ 24.90`.
5. Verificación del total, detalle, stock y páginas de listado y detalle.
6. Eliminación de la data temporal y restauración del stock.

| Verificación | Resultado obtenido |
|---|---|
| Estado inicial de MySQL | 0 clientes, 3 proveedores, 8 productos, 0 ventas, 0 detalles |
| Cliente temporal | Registrado correctamente |
| Producto utilizado | ID 1, precio `S/ 24.90`, stock inicial 40 |
| Venta registrada | 1 venta, 1 detalle, cantidad 2 |
| Total calculado | `S/ 49.80` |
| Subtotal | `S/ 49.80` |
| Stock final | 38 |
| Página de detalle | Cliente y datos de la venta visibles |
| Listado de ventas | Venta temporal visible |
| Limpieza | Venta y cliente eliminados; stock restaurado a 40 |
| Estado final de MySQL | 0 clientes, 3 proveedores, 8 productos, 0 ventas, 0 detalles |

## 10. Correcciones realizadas durante la verificación

| Defecto | Causa | Corrección | Estado |
|---|---|---|---|
| Ruta de proveedor generaba un parámetro incorrecto | Laravel pluralizaba `proveedor` como `proveedore` | Se definió el parámetro explícito `{proveedor}` en la ruta de recursos | Corregido |
| Aserción de fecha inconsistente en SQLite | Diferencia de formato entre MySQL y SQLite | Se ajustó la prueba para comparar la fecha según el formato de SQLite; no se modificó la lógica de la aplicación | Corregido |
| Error 500 al registrar una venta válida | La clausura de la transacción no capturaba `$calculadora` | Se cambió `use ($datos)` por `use ($datos, $calculadora)` | Corregido y verificado |

## 11. Limitaciones

- No se automatizaron pruebas de navegador para las interacciones JavaScript; el formulario y sus recursos fueron verificados mediante HTTP y revisión del comportamiento renderizado.
- La prueba de concurrencia y carga no forma parte del alcance. La venta usa transacción y `lockForUpdate`, pero no se sometió a múltiples usuarios simultáneos.
- La suite automatizada usa SQLite; la verificación real se complementó con MySQL para evitar depender de un solo motor.
- No se implementó autenticación, autorización ni roles.
- Las ventas no se editan ni eliminan desde la interfaz por decisión de diseño.
- La cobertura no incluye Blade, CSS ni JavaScript.
- El informe académico PAU3 no forma parte de este documento.

## 12. Comandos de reproducción

```powershell
# Suite completa
php artisan test

# Solo pruebas unitarias
php artisan test --testsuite=Unit

# Solo pruebas funcionales y de caja blanca
php artisan test --testsuite=Feature

# Caja blanca
php artisan test tests/Feature/CajaBlancaTest.php

# Cobertura con Xdebug
$env:XDEBUG_MODE='coverage'
php artisan test --coverage

# Rutas registradas
php artisan route:list --except-vendor
```

Las instrucciones de instalación y uso se encuentran en [README.md](../README.md).

## 13. Conclusión

La aplicación cumple con los módulos y reglas solicitadas. Las 97 pruebas automatizadas finalizaron sin fallos, con 348 aserciones y una cobertura de líneas reportada de 98.9 %. La verificación integral contra MySQL confirmó el registro de clientes y ventas, el cálculo correcto del total, el descuento de stock, el bloqueo de eliminaciones con relaciones y la restauración de la data de prueba. Los defectos encontrados durante la verificación fueron corregidos y vueltos a comprobar.
