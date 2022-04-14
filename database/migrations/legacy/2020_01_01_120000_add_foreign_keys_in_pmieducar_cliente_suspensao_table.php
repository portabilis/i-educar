<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarClienteSuspensaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.cliente_suspensao', function (Blueprint $table) {
            $table->foreign('ref_cod_motivo_suspensao')
                ->references('cod_motivo_suspensao')
                ->on('pmieducar.motivo_suspensao')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_cliente')
                ->references('cod_cliente')
                ->on('pmieducar.cliente')
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
        Schema::table('pmieducar.cliente_suspensao', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_motivo_suspensao']);
            $table->dropForeign(['ref_cod_cliente']);
        });
    }
}
