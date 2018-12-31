<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPortalPessoaFjPessoaAtividadeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal.pessoa_fj_pessoa_atividade', function (Blueprint $table) {
            $table->foreign('ref_cod_pessoa_fj')
               ->references('idpes')
               ->on('cadastro.juridica')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_pessoa_atividade')
               ->references('cod_pessoa_atividade')
               ->on('portal.pessoa_atividade')
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
        Schema::table('portal.pessoa_fj_pessoa_atividade', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_pessoa_fj']);
            $table->dropForeign(['ref_cod_pessoa_atividade']);
        });
    }
}
