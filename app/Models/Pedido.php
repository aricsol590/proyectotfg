<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    // Elimina o comenta esta línea si la tenías:
    // public $timestamps = false;

    // Si no necesitas asignar manualmente created_at/updated_at, no hace falta
    // definir $fillable para ellos; Laravel los gestionará automáticamente.

    protected $fillable = [
        'id_repartidor',
        'direccion',
        'telefono',
        'estado',
    ];

    // Asegúrate de que Laravel los castee a Carbon:
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function repartidor()
    {
        return $this->belongsTo(Repartidor::class, 'id_repartidor');
    }

    public function productos()
    {
        return $this->belongsToMany(
            Producto::class,
            'pedido_producto',
            'id_pedido',
            'id_producto'
        )->withPivot('cantidad');
    }
}
