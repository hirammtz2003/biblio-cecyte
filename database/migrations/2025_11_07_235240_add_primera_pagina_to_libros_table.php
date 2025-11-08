<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('libros', function (Blueprint $table) {
            $table->string('ruta_primera_pagina')->nullable()->after('ruta_archivo');
            $table->boolean('primera_pagina_generada')->default(false)->after('ruta_primera_pagina');
        });
    }

    public function down()
    {
        Schema::table('libros', function (Blueprint $table) {
            $table->dropColumn(['ruta_primera_pagina', 'primera_pagina_generada']);
        });
    }
};