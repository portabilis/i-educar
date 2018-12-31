<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPmiotopicReuniaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmiotopic.reuniao', function (Blueprint $table) {
            $table->foreign(['ref_moderador', 'ref_grupos_moderador'])
               ->references(['ref_ref_cod_pessoa_fj', 'ref_cod_grupos'])
               ->on('pmiotopic.grupomoderador')
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
        Schema::table('pmiotopic.reuniao', function (Blueprint $table) {
            $table->dropForeign(['ref_moderador', 'ref_grupos_moderador']);
        });
    }
}
