<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pedido_producto', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pedido');
            $table->unsignedBigInteger('id_producto');

            // Marcamos la clave primaria compuesta
            $table->primary(['id_pedido', 'id_producto']);

            // Añadimos los constraints como FK
            $table->foreign('id_pedido')
                  ->references('id')
                  ->on('pedidos')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->foreign('id_producto')
                  ->references('id')
                  ->on('productos')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            // El resto de columnas
            $table->integer('cantidad');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_producto');
    }
};
