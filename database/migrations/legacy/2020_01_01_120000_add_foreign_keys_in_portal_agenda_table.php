<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPortalAgendaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal.agenda', function (Blueprint $table) {
            $table->foreign('ref_ref_cod_pessoa_own')
                ->references('ref_cod_pessoa_fj')
                ->on('portal.funcionario')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_ref_cod_pessoa_exc')
                ->references('ref_cod_pessoa_fj')
                ->on('portal.funcionario')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_ref_cod_pessoa_cad')
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
        Schema::table('portal.agenda', function (Blueprint $table) {
            $table->dropForeign(['ref_ref_cod_pessoa_own']);
            $table->dropForeign(['ref_ref_cod_pessoa_exc']);
            $table->dropForeign(['ref_ref_cod_pessoa_cad']);
        });
    }
}
