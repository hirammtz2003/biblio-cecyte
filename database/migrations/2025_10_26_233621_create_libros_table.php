<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('libros', function (Blueprint $table) {
            $table->id();
            
            // Información básica del libro
            $table->string('titulo', 255);
            $table->string('autor', 255);
            $table->year('anio_publicacion');
            $table->text('descripcion')->nullable();
            $table->string('isbn', 20)->nullable()->unique();
            
            // Información académica
            $table->enum('carrera', [
                'Soporte y Mantenimiento de Equipo de Cómputo',
                'Enfermería General', 
                'Ventas',
                'Diseño Gráfico Digital',
                'General'
            ]);
            $table->enum('semestre', ['1°', '2°', '3°', '4°', '5°', '6°']);
            $table->string('materia', 150);
            
            // Archivo PDF
            $table->string('nombre_archivo'); // Nombre original del archivo
            $table->string('ruta_archivo');   // Ruta donde se almacena el PDF
            $table->string('hash_archivo')->unique(); // Hash para evitar duplicados
            $table->unsignedBigInteger('tamanio'); // Tamaño en bytes
            
            // Metadatos y relaciones
            $table->foreignId('id_usuario')->constrained('usuarios')->onDelete('cascade');
            $table->boolean('activo')->default(true);
            $table->integer('veces_descargado')->default(0);
            $table->integer('veces_visto')->default(0);
            
            $table->timestamps();
            
            // Índices para optimizar búsquedas
            $table->index(['carrera', 'semestre']);
            $table->index(['titulo']);
            $table->index(['autor']);
            $table->index(['materia']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('libros');
    }
};