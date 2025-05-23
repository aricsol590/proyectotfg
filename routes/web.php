<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PedidosController;

// Ruta principal: redirige al índice de pedidos
Route::get('/', [PedidosController::class, 'index']);

// Mostrar totales por repartidor antes de la resource para evitar conflictos
Route::get('pedidos/cuenta-repartidor', [PedidosController::class, 'CuentaRepartidor'])
     ->name('pedidos.cuentaRepartidor');

// Rutas de Pedidos (públicas)
Route::resource('pedidos', PedidosController::class);

// Rutas de perfil de usuario
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

// Rutas de Productos (protegidas, si usas middleware de auth, puedes agruparlas)
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create');
Route::post('/productos', [ProductoController::class, 'crearPedido'])->name('productos.crearPedido');
Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
Route::delete('/productos/{producto}', [ProductoController::class, 'delete'])->name('productos.delete');
Route::get('/productos/{producto}', [ProductoController::class, 'show'])->name('productos.show');
