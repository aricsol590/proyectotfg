<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    // Deshabilitar el uso de timestamps (created_at y updated_at)
    public $timestamps = false;

    // Añadimos 'tipo' a la lista de columnas que se pueden asignar de manera masiva
    protected $fillable = ['nombre', 'precio', 'tipo'];

    // Relación con Pedido a través de la tabla intermedia PedidoProducto
    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class, 'pedido_producto', 'id_producto', 'id_pedido')
                    ->withPivot('cantidad');
    }
}
