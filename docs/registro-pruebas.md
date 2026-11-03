# Registro de pruebas

**Proyecto:** Sistema de Gestión de Ventas - Tienda Peruana  
**Fecha de ejecución:** 2 de noviembre de 2026  
**Alcance:** inventario de las pruebas automatizadas y de las comprobaciones de accesibilidad de la fase 8, con su resultado  
**Documento relacionado:** [verificacion-tecnica.md](verificacion-tecnica.md) y [accesibilidad-fase-8.md](accesibilidad-fase-8.md)

Este registro sirve como insumo para redactar el informe de pruebas. No reemplaza al informe académico PAU3 ni al informe técnico de verificación.

## 1. Entorno y comando de ejecución

| Elemento | Configuración |
|---|---|
| Sistema operativo | Windows, entorno XAMPP |
| PHP | 8.2.12 |
| Laravel | 12.70.2 |
| Framework de pruebas | PHPUnit 11.5.57 |
| Base de datos de pruebas | SQLite en memoria mediante `phpunit.xml` |
| Base de datos de verificación integral | MySQL/MariaDB de XAMPP, base `tienda_peruana` |
| Navegador de la verificación de fase 8 | Chrome 157 en modo headless mediante DevTools |
| Zona horaria | `America/Lima` |
| Idioma de la aplicación | Español |

Comando ejecutado para la suite completa:

```powershell
php artisan test
```

Resultado obtenido:

```text
Tests: 107 passed (604 assertions)
Duration: 8.42s
```

También se generó un reporte JUnit con `vendor\phpunit\phpunit\phpunit --log-junit`, que confirmó 107 pruebas, 604 aserciones, 0 errores, 0 fallos y 0 pruebas omitidas.

## 2. Resumen por suite

| Suite | Pruebas | Aserciones | Aprobadas | Fallidas | Resultado |
|---|---:|---:|---:|---:|---|
| Unit | 29 | 48 | 29 | 0 | Aprobado |
| Feature | 78 | 556 | 78 | 0 | Aprobado |
| **Total** | **107** | **604** | **107** | **0** | **Aprobado** |

## 3. Tipos de prueba y distribución

| Tipo de prueba | Archivos principales | Pruebas | Aserciones | Resultado |
|---|---|---:|---:|---|
| Unitaria de cálculo | `tests/Unit/CalculadoraVentaTest.php` | 12 | 16 | Aprobado |
| Unitaria de validación | `tests/Unit/ValidacionReglasTest.php` | 16 | 31 | Aprobado |
| Prueba base unitaria | `tests/Unit/ExampleTest.php` | 1 | 1 | Aprobado |
| Caja negra: inicio y respuesta base | `tests/Feature/HomePageTest.php`, `tests/Feature/ExampleTest.php` | 5 | 19 | Aprobado |
| Caja negra: clientes | `tests/Feature/ClienteCrudTest.php` | 10 | 51 | Aprobado |
| Caja negra: proveedores | `tests/Feature/ProveedorCrudTest.php` | 10 | 50 | Aprobado |
| Caja negra: productos | `tests/Feature/ProductoCrudTest.php` | 12 | 57 | Aprobado |
| Caja negra: ventas | `tests/Feature/VentaCrudTest.php` | 10 | 55 | Aprobado |
| Integridad y relaciones de base de datos | `tests/Feature/DatabaseRelationshipsTest.php` | 5 | 14 | Aprobado |
| Datos demo e idempotencia | `tests/Feature/DatosDemoTest.php` | 1 | 24 | Aprobado |
| Caja blanca de controladores | `tests/Feature/CajaBlancaTest.php` | 16 | 54 | Aprobado |
| Accesibilidad automatizada | `tests/Feature/AccesibilidadTest.php` | 9 | 232 | Aprobado |
| Navegador real de la fase 8 | Chrome DevTools, fuera de PHPUnit | 27 comprobaciones + 11 páginas | No aplica | Aprobado |
| **Total automatizado** |  | **107** | **604** | **Aprobado** |

Las dos pruebas `ExampleTest` son las pruebas base generadas por Laravel. Las 105 restantes validan comportamiento del sistema.

### 3.1 Conciliación con el informe técnico anterior

El informe de las fases 1 a 7 registró 97 pruebas y 348 aserciones. La diferencia corresponde a las pruebas incorporadas después:

| Corte | Pruebas | Aserciones |
|---|---:|---:|
| Fases 1 a 7 | 97 | 348 |
| Fase 8: `AccesibilidadTest` | +9 | +232 |
| Datos demo: `DatosDemoTest` | +1 | +24 |
| **Total actual** | **107** | **604** |

## 4. Pruebas unitarias

### 4.1 Cálculo de ventas

Archivo: `tests/Unit/CalculadoraVentaTest.php`  
Tipo: unitaria de cálculo.  
Resultado del grupo: **12 pruebas, 16 aserciones, 0 fallos**.

| N.º | Método | Qué verifica | Aserciones | Resultado |
|---:|---|---|---:|---|
| 1 | `test_normaliza_un_precio_a_dos_decimales` | Convierte el valor `"24.90"` a `24.90`. | 1 | Aprobado |
| 2 | `test_calcula_el_subtotal_de_un_producto` | Calcula `24.90 × 2 = 49.80`. | 1 | Aprobado |
| 3 | `test_calcula_el_subtotal_con_precio_decimal_y_varias_unidades` | Calcula `"4.20" × 3 = 12.60`. | 1 | Aprobado |
| 4 | `test_calcula_el_total_de_varios_subtotales` | Suma `49.80 + 12.60 = 62.40`. | 1 | Aprobado |
| 5 | `test_calcula_el_total_sin_errores_de_redondeo` | Suma `0.10 + 0.10 + 0.10 = 0.30`. | 1 | Aprobado |
| 6 | `test_un_total_sin_subtotales_es_cero` | Un total sin subtotales devuelve `0.0`. | 1 | Aprobado |
| 7 | `test_rechaza_una_cantidad_igual_a_cero` | Rechaza cantidad cero con `InvalidArgumentException` y mensaje específico. | 2 | Aprobado |
| 8 | `test_rechaza_una_cantidad_negativa` | Rechaza cantidad negativa con `InvalidArgumentException` y mensaje específico. | 2 | Aprobado |
| 9 | `test_rechaza_un_precio_igual_a_cero` | Rechaza precio cero con `InvalidArgumentException` y mensaje específico. | 2 | Aprobado |
| 10 | `test_rechaza_un_precio_negativo` | Rechaza precio negativo al normalizarlo. | 2 | Aprobado |
| 11 | `test_rechaza_un_precio_no_numerico` | Rechaza un precio no numérico. | 1 | Aprobado |
| 12 | `test_rechaza_un_subtotal_negativo` | Rechaza un subtotal negativo dentro del total. | 1 | Aprobado |

### 4.2 Reglas de validación

Archivo: `tests/Unit/ValidacionReglasTest.php`  
Tipo: unitaria de validación de FormRequests.  
Resultado del grupo: **16 pruebas, 31 aserciones, 0 fallos**.

| N.º | Método | Qué verifica | Aserciones | Resultado |
|---:|---|---|---:|---|
| 1 | `test_client_request_accepts_valid_data` | Acepta datos válidos de cliente. | 1 | Aprobado |
| 2 | `test_client_request_rejects_missing_required_fields` | Rechaza nombres, apellidos y documento vacíos. | 4 | Aprobado |
| 3 | `test_client_request_rejects_invalid_email_and_phone` | Rechaza correo inválido y teléfono demasiado corto con mensajes específicos. | 3 | Aprobado |
| 4 | `test_provider_request_accepts_valid_data` | Acepta datos válidos de proveedor. | 1 | Aprobado |
| 5 | `test_provider_request_rejects_duplicate_ruc` | Rechaza un RUC ya registrado. | 2 | Aprobado |
| 6 | `test_product_request_accepts_valid_data` | Acepta datos válidos de producto. | 1 | Aprobado |
| 7 | `test_product_request_rejects_price_equal_to_zero` | Rechaza precio igual a cero. | 2 | Aprobado |
| 8 | `test_product_request_rejects_negative_price` | Rechaza precio negativo. | 2 | Aprobado |
| 9 | `test_product_request_rejects_negative_stock` | Rechaza stock negativo. | 2 | Aprobado |
| 10 | `test_product_request_rejects_a_nonexistent_provider` | Rechaza un proveedor inexistente. | 2 | Aprobado |
| 11 | `test_sale_request_accepts_valid_data` | Acepta una venta con cliente y productos válidos. | 1 | Aprobado |
| 12 | `test_sale_request_rejects_a_missing_client` | Rechaza una venta sin cliente. | 2 | Aprobado |
| 13 | `test_sale_request_rejects_an_empty_product_list` | Rechaza una venta sin productos. | 2 | Aprobado |
| 14 | `test_sale_request_rejects_quantity_equal_to_zero` | Rechaza cantidad cero en el detalle. | 2 | Aprobado |
| 15 | `test_sale_request_rejects_a_nonexistent_product` | Rechaza un producto inexistente. | 2 | Aprobado |
| 16 | `test_sale_request_rejects_a_repeated_product` | Rechaza el mismo producto repetido en la venta. | 2 | Aprobado |

### 4.3 Prueba base unitaria

Archivo: `tests/Unit/ExampleTest.php`  
Resultado del grupo: **1 prueba, 1 aserción, 0 fallos**.

| N.º | Método | Qué verifica | Aserciones | Resultado |
|---:|---|---|---:|---|
| 1 | `test_that_true_is_true` | Comprobación base del framework de pruebas. | 1 | Aprobado |

## 5. Pruebas de caja negra

Las pruebas de caja negra ejercitan rutas HTTP, formularios, redirecciones, mensajes y persistencia sin depender de las ramas internas del controlador.

### 5.1 Inicio y respuesta base

Archivos: `tests/Feature/HomePageTest.php` y `tests/Feature/ExampleTest.php`  
Resultado del grupo: **5 pruebas, 19 aserciones, 0 fallos**.

| N.º | Método | Qué verifica | Aserciones | Resultado |
|---:|---|---|---:|---|
| 1 | `test_home_page_loads_successfully` | `GET /` responde 200 y renderiza la vista `home`. | 2 | Aprobado |
| 2 | `test_home_page_shows_the_application_identity` | Muestra el nombre del sistema, el carácter académico y la identidad de tienda peruana. | 3 | Aprobado |
| 3 | `test_home_page_shows_the_initial_navigation` | Muestra Inicio, Clientes, Proveedores, Productos, Ventas, navegación etiquetada y botón móvil. | 7 | Aprobado |
| 4 | `test_home_page_shows_the_prepared_modules_and_categories` | Muestra los módulos preparados y las categorías de ejemplo. | 6 | Aprobado |
| 5 | `test_the_application_returns_a_successful_response` | Prueba base: `GET /` responde 200. | 1 | Aprobado |

### 5.2 Clientes

Archivo: `tests/Feature/ClienteCrudTest.php`  
Resultado del grupo: **10 pruebas, 51 aserciones, 0 fallos**.

| N.º | Método | Qué verifica | Aserciones | Resultado |
|---:|---|---|---:|---|
| 1 | `test_the_client_list_shows_registered_clients` | El listado muestra nombre, apellidos, documento y correo del cliente. | 6 | Aprobado |
| 2 | `test_a_valid_client_can_be_registered` | Registra un cliente válido, redirige, informa éxito y persiste los datos. | 5 | Aprobado |
| 3 | `test_a_client_can_be_registered_without_optional_contact_data` | Permite registrar un cliente sin correo, teléfono ni dirección. | 4 | Aprobado |
| 4 | `test_required_fields_are_validated_when_registering` | Exige nombres, apellidos y documento; no exige los campos opcionales. | 8 | Aprobado |
| 5 | `test_duplicate_documents_are_rejected` | Rechaza un documento duplicado y conserva un solo cliente. | 3 | Aprobado |
| 6 | `test_email_format_and_phone_length_are_validated` | Rechaza correo inválido y teléfono corto. | 4 | Aprobado |
| 7 | `test_the_edit_form_shows_the_client_data` | El formulario de edición muestra los datos y la ruta de actualización. | 7 | Aprobado |
| 8 | `test_a_client_can_be_updated` | Actualiza los datos, redirige, informa éxito y persiste los cambios. | 5 | Aprobado |
| 9 | `test_a_client_without_sales_can_be_deleted` | Elimina un cliente sin ventas y confirma el mensaje de éxito. | 4 | Aprobado |
| 10 | `test_a_client_with_sales_cannot_be_deleted` | Bloquea la eliminación de un cliente con ventas y conserva ambos registros. | 5 | Aprobado |

### 5.3 Proveedores

Archivo: `tests/Feature/ProveedorCrudTest.php`  
Resultado del grupo: **10 pruebas, 50 aserciones, 0 fallos**.

| N.º | Método | Qué verifica | Aserciones | Resultado |
|---:|---|---|---:|---|
| 1 | `test_the_provider_list_shows_registered_providers` | El listado muestra nombre, RUC, correo y dirección del proveedor. | 6 | Aprobado |
| 2 | `test_a_valid_provider_can_be_registered` | Registra un proveedor válido, redirige, informa éxito y persiste los datos. | 5 | Aprobado |
| 3 | `test_a_provider_can_be_registered_without_optional_contact_data` | Permite registrar un proveedor sin correo, teléfono ni dirección. | 4 | Aprobado |
| 4 | `test_required_fields_are_validated_when_registering` | Exige nombre y RUC; no exige los campos opcionales. | 7 | Aprobado |
| 5 | `test_duplicate_ruc_is_rejected` | Rechaza un RUC duplicado y conserva un solo proveedor. | 3 | Aprobado |
| 6 | `test_email_format_and_phone_length_are_validated` | Rechaza correo inválido y teléfono corto. | 4 | Aprobado |
| 7 | `test_the_edit_form_shows_the_provider_data` | El formulario de edición muestra los datos y la ruta de actualización. | 7 | Aprobado |
| 8 | `test_a_provider_can_be_updated` | Actualiza los datos, redirige, informa éxito y persiste los cambios. | 5 | Aprobado |
| 9 | `test_a_provider_without_products_can_be_deleted` | Elimina un proveedor sin productos y confirma el mensaje de éxito. | 4 | Aprobado |
| 10 | `test_a_provider_with_products_cannot_be_deleted` | Bloquea la eliminación de un proveedor con productos y conserva ambos registros. | 5 | Aprobado |

### 5.4 Productos

Archivo: `tests/Feature/ProductoCrudTest.php`  
Resultado del grupo: **12 pruebas, 57 aserciones, 0 fallos**.

| N.º | Método | Qué verifica | Aserciones | Resultado |
|---:|---|---|---:|---|
| 1 | `test_the_product_list_shows_registered_products` | El listado muestra nombre, descripción, categoría, precio y proveedor. | 7 | Aprobado |
| 2 | `test_a_valid_product_can_be_registered` | Registra un producto válido y persiste nombre, categoría, precio, stock y proveedor. | 5 | Aprobado |
| 3 | `test_a_product_can_be_registered_without_a_description` | Permite registrar un producto sin descripción. | 4 | Aprobado |
| 4 | `test_required_fields_are_validated_when_registering` | Exige nombre, categoría, precio, stock y proveedor; la descripción es opcional. | 8 | Aprobado |
| 5 | `test_a_negative_price_is_rejected` | Rechaza precio negativo y no persiste el producto. | 3 | Aprobado |
| 6 | `test_a_price_equal_to_zero_is_rejected` | Rechaza precio igual a cero y no persiste el producto. | 3 | Aprobado |
| 7 | `test_negative_stock_is_rejected` | Rechaza stock negativo y no persiste el producto. | 3 | Aprobado |
| 8 | `test_a_nonexistent_provider_is_rejected` | Rechaza un proveedor inexistente y no persiste el producto. | 3 | Aprobado |
| 9 | `test_the_edit_form_shows_the_product_data` | El formulario de edición muestra los datos y la ruta de actualización. | 7 | Aprobado |
| 10 | `test_a_product_can_be_updated` | Actualiza nombre, precio, stock y proveedor; persiste los cambios. | 5 | Aprobado |
| 11 | `test_a_product_without_sales_can_be_deleted` | Elimina un producto sin detalles de venta. | 4 | Aprobado |
| 12 | `test_a_product_used_in_a_sale_cannot_be_deleted` | Bloquea la eliminación de un producto usado en una venta y conserva producto y detalle. | 5 | Aprobado |

### 5.5 Ventas

Archivo: `tests/Feature/VentaCrudTest.php`  
Resultado del grupo: **10 pruebas, 55 aserciones, 0 fallos**.

| N.º | Método | Qué verifica | Aserciones | Resultado |
|---:|---|---|---:|---|
| 1 | `test_the_sale_list_shows_registered_sales` | El listado muestra código, cliente, fecha, total y enlace al detalle. | 8 | Aprobado |
| 2 | `test_a_valid_sale_is_registered_and_amounts_are_calculated_on_the_server` | Ignora precios enviados por el cliente, calcula subtotales y total, persiste la venta y descuenta stock. | 11 | Aprobado |
| 3 | `test_a_sale_without_a_client_is_rejected` | Rechaza una venta sin cliente y no crea venta ni detalle. | 4 | Aprobado |
| 4 | `test_a_nonexistent_client_is_rejected` | Rechaza un cliente inexistente y no crea la venta. | 3 | Aprobado |
| 5 | `test_a_sale_without_products_is_rejected` | Rechaza una venta sin productos y no crea registros. | 4 | Aprobado |
| 6 | `test_a_quantity_equal_to_zero_is_rejected` | Rechaza cantidad cero y no crea la venta. | 3 | Aprobado |
| 7 | `test_a_negative_quantity_is_rejected` | Rechaza cantidad negativa y no crea la venta. | 3 | Aprobado |
| 8 | `test_a_nonexistent_product_is_rejected` | Rechaza un producto inexistente y no crea venta ni detalle. | 4 | Aprobado |
| 9 | `test_a_sale_exceeding_the_available_stock_is_rejected` | Rechaza stock insuficiente, no crea registros y conserva el stock. | 5 | Aprobado |
| 10 | `test_the_sale_detail_shows_products_quantities_and_amounts` | El detalle muestra código, cliente, fecha, producto, categoría, precio, cantidad y subtotal. | 10 | Aprobado |

### 5.6 Integridad y relaciones de base de datos

Archivo: `tests/Feature/DatabaseRelationshipsTest.php`  
Resultado del grupo: **5 pruebas, 14 aserciones, 0 fallos**.

| N.º | Método | Qué verifica | Aserciones | Resultado |
|---:|---|---|---:|---|
| 1 | `test_models_expose_the_expected_relationships` | Comprueba las relaciones proveedor-producto, cliente-venta, venta-detalle y producto-detalle. | 8 | Aprobado |
| 2 | `test_deleting_a_sale_cascades_its_details` | Eliminar una venta elimina sus detalles y conserva cliente y producto. | 3 | Aprobado |
| 3 | `test_a_provider_with_products_cannot_be_deleted` | La base de datos impide eliminar un proveedor con productos. | 1 | Aprobado |
| 4 | `test_a_client_with_sales_cannot_be_deleted` | La base de datos impide eliminar un cliente con ventas. | 1 | Aprobado |
| 5 | `test_a_product_used_in_a_sale_cannot_be_deleted` | La base de datos impide eliminar un producto usado en una venta. | 1 | Aprobado |

## 6. Pruebas de datos demo

Archivo: `tests/Feature/DatosDemoTest.php`  
Tipo: integración de seeders e idempotencia.  
Resultado del grupo: **1 prueba, 24 aserciones, 0 fallos**.

| N.º | Método | Qué verifica | Aserciones | Resultado |
|---:|---|---|---:|---|
| 1 | `test_los_datos_demo_se_cargan_sin_duplicarse` | Ejecuta dos veces el seeder de clientes y ventas; confirma 8 productos, 6 clientes, 8 ventas y 20 detalles; valida que cada total coincida con la suma de sus subtotales, que los documentos tengan 8 dígitos y que los listados muestren los datos demo. | 24 | Aprobado |

## 7. Pruebas de caja blanca

Archivo: `tests/Feature/CajaBlancaTest.php`  
Tipo: caja blanca de controladores, ramas de error, transacciones y caminos defensivos.  
Resultado del grupo: **16 pruebas, 54 aserciones, 0 fallos**.

| N.º | Método | Qué verifica | Aserciones | Resultado |
|---:|---|---|---:|---|
| 1 | `test_client_controller_takes_the_create_branch` | Rama de creación de clientes: `GET /clientes/create` responde 200. | 1 | Aprobado |
| 2 | `test_provider_controller_takes_the_create_branch` | Rama de creación de proveedores: `GET /proveedores/create` responde 200. | 1 | Aprobado |
| 3 | `test_product_controller_takes_the_create_branch` | Rama de creación de productos: `GET /productos/create` responde 200. | 1 | Aprobado |
| 4 | `test_sale_controller_takes_the_create_branch` | Rama de creación de ventas: `GET /ventas/create` responde 200. | 1 | Aprobado |
| 5 | `test_product_controller_takes_the_blocked_deletion_branch_when_details_exist` | Producto con detalles de venta: bloquea la eliminación, informa el motivo y conserva el registro. | 3 | Aprobado |
| 6 | `test_product_controller_takes_the_deletion_branch_when_there_are_no_details` | Producto sin detalles: elimina, informa éxito y confirma la ausencia en base de datos. | 3 | Aprobado |
| 7 | `test_provider_controller_takes_the_blocked_deletion_branch_when_products_exist` | Proveedor con productos: bloquea la eliminación, informa el motivo y conserva el registro. | 3 | Aprobado |
| 8 | `test_provider_controller_takes_the_deletion_branch_when_there_are_no_products` | Proveedor sin productos: elimina, informa éxito y confirma la ausencia en base de datos. | 3 | Aprobado |
| 9 | `test_client_controller_takes_the_blocked_deletion_branch_when_sales_exist` | Cliente con ventas: bloquea la eliminación, informa el motivo y conserva el registro. | 3 | Aprobado |
| 10 | `test_client_controller_takes_the_deletion_branch_when_there_are_no_sales` | Cliente sin ventas: elimina, informa éxito y confirma la ausencia en base de datos. | 3 | Aprobado |
| 11 | `test_client_controller_takes_the_database_error_branch` | Simula `QueryException` al eliminar un cliente y devuelve el mensaje de registros relacionados. | 5 | Aprobado |
| 12 | `test_provider_controller_takes_the_database_error_branch` | Simula `QueryException` al eliminar un proveedor y devuelve el mensaje de registros relacionados. | 5 | Aprobado |
| 13 | `test_product_controller_takes_the_database_error_branch` | Simula `QueryException` al eliminar un producto y devuelve el mensaje de registros relacionados. | 5 | Aprobado |
| 14 | `test_sale_controller_takes_the_defensive_missing_product_branch` | Producto inexistente dentro de la transacción: lanza validación y no crea venta ni detalle. | 4 | Aprobado |
| 15 | `test_sale_controller_takes_the_stock_error_branch_and_rolls_back_all_changes` | Stock insuficiente dentro de la transacción: revierte todo y conserva el stock original. | 5 | Aprobado |
| 16 | `test_sale_controller_takes_the_success_branch_and_persists_all_changes` | Venta exitosa: persiste venta y detalle, calcula total, descuenta stock y redirige al detalle. | 8 | Aprobado |

## 8. Pruebas de accesibilidad de la fase 8

### 8.1 Pruebas automatizadas de marcado y estilos

Archivo: `tests/Feature/AccesibilidadTest.php`  
Tipo: integración de accesibilidad sobre rutas y vistas Blade.  
Resultado del grupo: **9 pruebas, 232 aserciones, 0 fallos**.

| N.º | Método | Qué verifica | Aserciones | Resultado |
|---:|---|---|---:|---|
| 1 | `test_all_pages_include_the_accessibility_panel_and_skip_link` | En 9 rutas verifica estado 200, enlace de salto, destino enfocable, panel, controles, textos de ayuda, etiqueta de modo oscuro, región de estado e idioma `es`. | 135 | Aprobado |
| 2 | `test_text_size_and_contrast_preferences_are_restored_before_rendering` | Comprueba que las preferencias de texto y contraste se leen de `localStorage` antes del render. | 5 | Aprobado |
| 3 | `test_forms_have_labels_required_indicators_and_help_text` | Verifica etiquetas asociadas, campos obligatorios, textos de ayuda y `aria-describedby` en formularios. | 17 | Aprobado |
| 4 | `test_validation_errors_are_visible_and_described_next_to_the_field` | Comprueba errores visibles, `role="alert"`, `aria-invalid` y descripción junto al campo. | 6 | Aprobado |
| 5 | `test_flash_messages_are_exposed_as_status_and_alert_regions` | Verifica mensajes de éxito como `role="status"`, errores como `role="alert"` y confirmación de eliminación. | 9 | Aprobado |
| 6 | `test_tables_and_navigation_expose_accessible_names` | Comprueba nombres accesibles en navegación, tablas, captions, encabezados y botones de acción. | 17 | Aprobado |
| 7 | `test_each_page_has_a_single_main_heading` | Verifica que 13 rutas tengan un solo `h1` y respondan 200. | 26 | Aprobado |
| 8 | `test_the_sale_form_announces_totals_and_adds_rows_with_keyboard_support` | Comprueba etiquetas del formulario de venta, región anunciadora, total accesible, botones para agregar y quitar líneas. | 10 | Aprobado |
| 9 | `test_the_accessibility_styles_keep_focus_and_touch_targets_visible` | Verifica foco visible, áreas táctiles, tema oscuro de alto contraste, `color-scheme: dark` y reducción de movimiento. | 7 | Aprobado |

### 8.2 Comprobaciones en navegador real

Se ejecutó Chrome 157 en modo headless mediante el protocolo DevTools sobre la aplicación servida en `http://127.0.0.1:8010`. Las comprobaciones se hicieron con scripts temporales de verificación; los scripts se eliminaron y las capturas se conservaron en `docs/capturas/`.

#### 8.2.1 Panel de accesibilidad y teclado

Resultado del grupo: **27 comprobaciones, 0 fallos**.

| N.º | Comprobación | Resultado |
|---:|---|---|
| 1 | El panel inicia oculto. | Aprobado |
| 2 | El panel está asociado al botón mediante `aria-controls`. | Aprobado |
| 3 | La etiqueta del control es «Modo oscuro de alto contraste». | Aprobado |
| 4 | El panel se abre al activar el botón. | Aprobado |
| 5 | El botón informa el estado abierto con `aria-expanded`. | Aprobado |
| 6 | El foco inicial se coloca en el botón de cierre. | Aprobado |
| 7 | El panel contiene 5 controles enfocables. | Aprobado |
| 8 | Tab permanece dentro del panel al llegar al último control. | Aprobado |
| 9 | Shift + Tab permanece dentro del panel al retroceder desde el primero. | Aprobado |
| 10 | El aumento de texto aplica el nivel 2. | Aprobado |
| 11 | El texto efectivo aumenta a 19.2 px. | Aprobado |
| 12 | El valor mostrado es 120 %. | Aprobado |
| 13 | La preferencia de texto se guarda en `localStorage`. | Aprobado |
| 14 | La región de estado anuncia el tamaño de texto. | Aprobado |
| 15 | La disminución de texto aplica el nivel 1. | Aprobado |
| 16 | El modo oscuro se activa. | Aprobado |
| 17 | El botón refleja el estado con `aria-pressed`. | Aprobado |
| 18 | El fondo aplicado es `rgb(11, 20, 17)`. | Aprobado |
| 19 | El texto aplicado es `rgb(243, 247, 245)`. | Aprobado |
| 20 | El esquema de color oscuro está declarado. | Aprobado |
| 21 | La preferencia de modo oscuro se guarda en `localStorage`. | Aprobado |
| 22 | Restaurar quita el tamaño aumentado. | Aprobado |
| 23 | Restaurar quita el modo oscuro. | Aprobado |
| 24 | Restaurar elimina la preferencia de texto guardada. | Aprobado |
| 25 | Restaurar elimina la preferencia de modo oscuro guardada. | Aprobado |
| 26 | Escape cierra el panel. | Aprobado |
| 27 | El foco vuelve al botón Accesibilidad. | Aprobado |

#### 8.2.2 Tema oscuro en páginas y contraste

| Comprobación | Cobertura | Resultado |
|---|---|---|
| Tema oscuro activo | 11 rutas del sistema | Aprobado |
| Un solo `h1` por página | 11 rutas | Aprobado |
| Sin desbordamiento horizontal | 11 rutas | Aprobado |
| Textos visibles con contraste suficiente | 128 textos revisados | 0 fallas |
| Comprobaciones adicionales del tema oscuro | 12 comprobaciones | 12/12 aprobadas |
| Combinaciones de contraste medidas | Tabla de [accesibilidad-fase-8.md](accesibilidad-fase-8.md) | AA/AAA, 0 fallas |

Las capturas de evidencia son:

- `docs/capturas/panel-accesibilidad.png`
- `docs/capturas/panel-alto-contraste.png`
- `docs/capturas/panel-texto-grande.png`
- `docs/capturas/panel-movil.png`
- `docs/capturas/clientes-modo-oscuro.png`
- `docs/capturas/ventas-modo-oscuro.png`

## 9. Resultado por módulo

| Módulo o área | Pruebas relacionadas | Resultado |
|---|---|---|
| Cálculo de ventas | 12 unitarias de cálculo | Aprobado |
| Validación de formularios | 16 unitarias de validación | Aprobado |
| Inicio y navegación | 5 de caja negra | Aprobado |
| Clientes | 10 de caja negra + 4 de caja blanca | Aprobado |
| Proveedores | 10 de caja negra + 4 de caja blanca | Aprobado |
| Productos | 12 de caja negra + 4 de caja blanca | Aprobado |
| Ventas | 10 de caja negra + 4 de caja blanca | Aprobado |
| Integridad de base de datos | 5 de relaciones y restricciones | Aprobado |
| Datos demo | 1 de integración e idempotencia | Aprobado |
| Accesibilidad | 9 automatizadas + 27 de navegador + 11 páginas | Aprobado |

## 10. Cobertura de referencia

El informe técnico de las fases 1 a 7 registró una cobertura de líneas de **98.9 %**, con **100 %** en los controladores, los FormRequests, los modelos utilizados y `CalculadoraVenta`. Esa medición no incluye vistas Blade, CSS ni JavaScript y no se recalculó en la ejecución actual.

## 11. Limitaciones del registro

- Las pruebas automatizadas usan SQLite en memoria; la verificación integral de MySQL se documenta en [verificacion-tecnica.md](verificacion-tecnica.md).
- Las interacciones JavaScript del panel y del tema oscuro se verificaron en Chrome mediante DevTools, no dentro de PHPUnit.
- Los scripts temporales de la verificación de navegador no forman parte del repositorio; se conservaron las capturas como evidencia.
- No se ejecutaron pruebas de carga, estrés ni concurrencia con múltiples usuarios.
- La cobertura no incluye Blade, CSS ni JavaScript.
- No se implementaron autenticación, autorización ni roles, por lo que no existen pruebas de esos aspectos.
- Las pruebas base `ExampleTest` se conservan para verificar el funcionamiento mínimo del framework.

## 12. Comandos de reproducción

```powershell
# Suite completa
php artisan test

# Solo pruebas unitarias
php artisan test --testsuite=Unit

# Solo pruebas funcionales
php artisan test --testsuite=Feature

# Accesibilidad
php artisan test tests/Feature/AccesibilidadTest.php

# Datos demo
php artisan test tests/Feature/DatosDemoTest.php

# Caja blanca
php artisan test tests/Feature/CajaBlancaTest.php

# Una prueba concreta
php artisan test --filter=test_los_datos_demo_se_cargan_sin_duplicarse

# Reporte JUnit
php vendor\phpunit\phpunit\phpunit --log-junit storage\logs\pruebas.xml
```

## 13. Conclusión

La suite automatizada finalizó con **107 pruebas aprobadas, 604 aserciones y 0 fallos**. La fase 8 aportó **9 pruebas automatizadas con 232 aserciones**, además de **27 comprobaciones de panel y teclado** y la revisión del tema oscuro en **11 páginas**, todas aprobadas. Los datos demo se verificaron con una prueba adicional de 24 aserciones, incluida la idempotencia de los seeders.
