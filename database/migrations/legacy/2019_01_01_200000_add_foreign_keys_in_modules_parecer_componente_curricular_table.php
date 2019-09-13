<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesParecerComponenteCurricularTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.parecer_componente_curricular', function (Blueprint $table) {
            $table->foreign('parecer_aluno_id')
               ->references('id')
               ->on('modules.parecer_aluno')
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
        Schema::table('modules.parecer_componente_curricular', function (Blueprint $table) {
            $table->dropForeign(['parecer_aluno_id']);
        });
    }
}
