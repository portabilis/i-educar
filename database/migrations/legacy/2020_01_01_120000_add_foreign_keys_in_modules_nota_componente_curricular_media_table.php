<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesNotaComponenteCurricularMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.nota_componente_curricular_media', function (Blueprint $table) {
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
        Schema::table('modules.nota_componente_curricular_media', function (Blueprint $table) {
            $table->dropForeign(['nota_aluno_id']);
        });
    }
}
