<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarClienteTipoExemplarTipoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.cliente_tipo_exemplar_tipo', function (Blueprint $table) {
            $table->foreign('ref_cod_exemplar_tipo')
                ->references('cod_exemplar_tipo')
                ->on('pmieducar.exemplar_tipo')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_cliente_tipo')
                ->references('cod_cliente_tipo')
                ->on('pmieducar.cliente_tipo')
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
        Schema::table('pmieducar.cliente_tipo_exemplar_tipo', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_exemplar_tipo']);
            $table->dropForeign(['ref_cod_cliente_tipo']);
        });
    }
}
