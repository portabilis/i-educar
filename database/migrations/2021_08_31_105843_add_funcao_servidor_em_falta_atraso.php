<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFuncaoServidorEmFaltaAtraso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.falta_atraso', function (Blueprint $table) {
            $table->integer('ref_cod_servidor_funcao')
                ->nullable();
            $table->foreign('ref_cod_servidor_funcao')
                ->on('pmieducar.servidor_funcao')
                ->references('cod_servidor_funcao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.falta_atraso', function (Blueprint $table) {
            $table->dropColumn('ref_cod_servidor_funcao');
        });
    }
}
