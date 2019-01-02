<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPortalMaillingFilaEnvioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal.mailling_fila_envio', function (Blueprint $table) {
            $table->foreign('ref_cod_mailling_email_conteudo')
               ->references('cod_mailling_email_conteudo')
               ->on('portal.mailling_email_conteudo');

            $table->foreign('ref_cod_mailling_email')
               ->references('cod_mailling_email')
               ->on('portal.mailling_email');

            $table->foreign('ref_ref_cod_pessoa_fj')
               ->references('ref_cod_pessoa_fj')
               ->on('portal.funcionario')
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
        Schema::table('portal.mailling_fila_envio', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_mailling_email_conteudo']);
            $table->dropForeign(['ref_cod_mailling_email']);
            $table->dropForeign(['ref_ref_cod_pessoa_fj']);
        });
    }
}
