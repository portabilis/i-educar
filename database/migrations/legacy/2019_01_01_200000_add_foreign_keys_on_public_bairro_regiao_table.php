<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPublicBairroRegiaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public.bairro_regiao', function (Blueprint $table) {
            $table->foreign('ref_idbai')
               ->references('idbai')
               ->on('bairro')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_regiao')
               ->references('cod_regiao')
               ->on('regiao')
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
        Schema::table('public.bairro_regiao', function (Blueprint $table) {
            $table->dropForeign(['ref_idbai']);
            $table->dropForeign(['ref_cod_regiao']);
        });
    }
}
