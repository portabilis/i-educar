<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.componente_curricular_ano_escolar', function (Blueprint $table) {
            $table->decimal('hora_falta', 7, 4)
                ->nullable()
                ->default(null);
        });

        Schema::table('pmieducar.escola_serie_disciplina', function (Blueprint $table) {
            $table->decimal('hora_falta', 7, 4)
                ->nullable()
                ->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.componente_curricular_ano_escolar', function (Blueprint $table) {
            $table->dropColumn([
                'hora_falta',
            ]);
        });

        Schema::table('pmieducar.escola_serie_disciplina', function (Blueprint $table) {
            $table->dropColumn([
                'hora_falta',
            ]);
        });

    }
};
