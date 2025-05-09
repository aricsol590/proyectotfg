<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repartidor extends Model
{
    use HasFactory;

    protected $table = 'repartidores'; 
    protected $fillable = ['nombre'];

   
    public function pedidos()
    {
        
        return $this->hasMany(Pedido::class, 'id_repartidor');
    }
}