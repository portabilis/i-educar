<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPortalImagemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal.imagem', function (Blueprint $table) {
            $table->foreign('ref_cod_pessoa_exc')
               ->references('ref_cod_pessoa_fj')
               ->on('portal.funcionario')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_pessoa_cad')
               ->references('ref_cod_pessoa_fj')
               ->on('portal.funcionario')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_imagem_tipo')
               ->references('cod_imagem_tipo')
               ->on('portal.imagem_tipo')
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
        Schema::table('portal.imagem', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_pessoa_exc']);
            $table->dropForeign(['ref_cod_pessoa_cad']);
            $table->dropForeign(['ref_cod_imagem_tipo']);
        });
    }
}
