<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellido_paterno', 100);
            $table->string('apellido_materno', 100);
            $table->enum('carrera', [
                'Soporte y Mantenimiento de Equipo de Cómputo',
                'Enfermería General', 
                'Ventas',
                'Diseño Gráfico Digital'
            ]);
            $table->string('numero_control', 20)->unique();
            $table->string('email', 150)->unique();
            $table->string('contrasena');
            $table->enum('tipo_usuario', ['Administrador', 'Docente', 'Alumno'])->default('Alumno');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
};