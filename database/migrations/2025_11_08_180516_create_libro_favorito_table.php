<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('libro_favorito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('libro_id')->constrained('libros')->onDelete('cascade');
            $table->timestamps();
            
            // Asegurar que un usuario no pueda marcar el mismo libro como favorito múltiples veces
            $table->unique(['usuario_id', 'libro_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('libro_favorito');
    }
};