<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPmieducarAbandonoTipoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.abandono_tipo', function (Blueprint $table) {
            $table->foreign('ref_usuario_exc')
               ->references('cod_usuario')
               ->on('pmieducar.usuario');

            $table->foreign('ref_usuario_cad')
               ->references('cod_usuario')
               ->on('pmieducar.usuario');

            $table->foreign('ref_cod_instituicao')
               ->references('cod_instituicao')
               ->on('pmieducar.instituicao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.abandono_tipo', function (Blueprint $table) {
            $table->dropForeign(['ref_usuario_exc']);
            $table->dropForeign(['ref_usuario_cad']);
            $table->dropForeign(['ref_cod_instituicao']);
        });
    }
}
