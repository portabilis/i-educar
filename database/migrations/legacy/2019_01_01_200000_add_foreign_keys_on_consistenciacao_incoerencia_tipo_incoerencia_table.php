<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnConsistenciacaoIncoerenciaTipoIncoerenciaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consistenciacao.incoerencia_tipo_incoerencia', function (Blueprint $table) {
            $table->foreign('id_tipo_inc')
               ->references('id_tipo_inc')
               ->on('consistenciacao.tipo_incoerencia');

            $table->foreign('idinc')
               ->references('idinc')
               ->on('consistenciacao.incoerencia')
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
        Schema::table('consistenciacao.incoerencia_tipo_incoerencia', function (Blueprint $table) {
            $table->dropForeign(['id_tipo_inc']);
            $table->dropForeign(['idinc']);
        });
    }
}
