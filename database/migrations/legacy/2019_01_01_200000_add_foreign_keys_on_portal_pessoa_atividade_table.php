<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPortalPessoaAtividadeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal.pessoa_atividade', function (Blueprint $table) {
            $table->foreign('ref_cod_ramo_atividade')
               ->references('cod_ramo_atividade')
               ->on('portal.pessoa_ramo_atividade')
               ->onUpdate('restrict')
               ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('portal.pessoa_atividade', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_ramo_atividade']);
        });
    }
}
