<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::resource('clientes', ClienteController::class)->except(['show']);
Route::resource('proveedores', ProveedorController::class)
    ->parameters(['proveedores' => 'proveedor'])
    ->except(['show']);
Route::resource('productos', ProductoController::class)
    ->parameters(['productos' => 'producto'])
    ->except(['show']);
Route::resource('ventas', VentaController::class)
    ->parameters(['ventas' => 'venta'])
    ->only(['index', 'create', 'store', 'show']);
