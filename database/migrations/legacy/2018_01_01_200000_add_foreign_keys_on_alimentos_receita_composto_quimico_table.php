<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosReceitaCompostoQuimicoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.receita_composto_quimico', function (Blueprint $table) {
            $table->foreign('idrec')
               ->references('idrec')
               ->on('alimentos.receita');

            $table->foreign('idcom')
               ->references('idcom')
               ->on('alimentos.composto_quimico');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alimentos.receita_composto_quimico', function (Blueprint $table) {
            $table->dropForeign(['idrec']);
            $table->dropForeign(['idcom']);
        });
    }
}
