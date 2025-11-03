<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('libros', function (Blueprint $table) {
            $table->boolean('descargable')->default(false)->after('activo');
        });
    }

    public function down()
    {
        Schema::table('libros', function (Blueprint $table) {
            $table->dropColumn('descargable');
        });
    }
};