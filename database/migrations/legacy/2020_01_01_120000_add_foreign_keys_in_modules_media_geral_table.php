<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesMediaGeralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.media_geral', function (Blueprint $table) {
            $table->foreign('nota_aluno_id')
                ->references('id')
                ->on('modules.nota_aluno')
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
        Schema::table('modules.media_geral', function (Blueprint $table) {
            $table->dropForeign(['nota_aluno_id']);
        });
    }
}
