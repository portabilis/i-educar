<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesCalendarioTurmaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.calendario_turma', function (Blueprint $table) {
            $table->foreign(['calendario_ano_letivo_id', 'mes', 'dia'])
                ->references(['ref_cod_calendario_ano_letivo', 'mes', 'dia'])
                ->on('pmieducar.calendario_dia')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.calendario_turma', function (Blueprint $table) {
            $table->dropForeign(['calendario_ano_letivo_id', 'mes', 'dia']);
        });
    }
}
