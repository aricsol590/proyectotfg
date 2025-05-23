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
        Schema::table('pedidos', function (Blueprint $table) {
            // Añadimos ambas marcas de tiempo al final de la tabla
            $table->timestamp('created_at')
                  ->nullable()
                  ->after('telefono')
                  ->useCurrent();

            $table->timestamp('updated_at')
                  ->nullable()
                  ->after('created_at')
                  ->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            //
        });
    }
};
