<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoProducto extends Model
{
    use HasFactory;

    // No se debe definir la clave primaria ya que es una tabla intermedia
    protected $table = 'pedido_producto';

    // Definir los campos que puedes llenar de manera masiva
    protected $fillable = ['id_pedido', 'id_producto', 'cantidad', 'precio_unitario'];

    // Relación con Pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    // Relación con Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
