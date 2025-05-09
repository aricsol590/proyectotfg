<?php

use App\Http\Controllers\PedidoController;
use Illuminate\Support\Facades\Route;

// Ruta para crear un pedido
Route::post('/crear-pedido', [PedidoController::class, 'crearPedido']);

// Ruta para ver un pedido
Route::get('/pedido/{id}', [PedidoController::class, 'verPedido']);
