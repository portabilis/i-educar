<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPmiotopicParticipanteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmiotopic.participante', function (Blueprint $table) {
            $table->foreign(['ref_ref_idpes', 'ref_ref_cod_grupos'])
               ->references(['ref_idpes', 'ref_cod_grupos'])
               ->on('pmiotopic.grupopessoa')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_reuniao')
               ->references('cod_reuniao')
               ->on('pmiotopic.reuniao')
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
        Schema::table('pmiotopic.participante', function (Blueprint $table) {
            $table->dropForeign(['ref_ref_idpes', 'ref_ref_cod_grupos']);
            $table->dropForeign(['ref_cod_reuniao']);
        });
    }
}
