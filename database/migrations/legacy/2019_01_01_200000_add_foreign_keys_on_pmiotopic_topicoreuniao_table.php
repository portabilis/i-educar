<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPmiotopicTopicoreuniaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmiotopic.topicoreuniao', function (Blueprint $table) {
            $table->foreign('ref_cod_topico')
               ->references('cod_topico')
               ->on('pmiotopic.topico')
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
        Schema::table('pmiotopic.topicoreuniao', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_topico']);
            $table->dropForeign(['ref_cod_reuniao']);
        });
    }
}
