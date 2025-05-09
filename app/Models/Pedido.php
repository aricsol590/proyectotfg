<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'id_repartidor',
        'direccion',
        'telefono',
        'estado',
    ];
    

    public function repartidor()
    {
        return $this->belongsTo(Repartidor::class, 'id_repartidor');
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'pedido_producto', 'id_pedido', 'id_producto')
                    ->withPivot('cantidad');
    }

}