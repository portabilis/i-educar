<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesFaltaComponenteCurricularTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.falta_componente_curricular', function (Blueprint $table) {
            $table->foreign('falta_aluno_id')
                ->references('id')
                ->on('modules.falta_aluno')
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
        Schema::table('modules.falta_componente_curricular', function (Blueprint $table) {
            $table->dropForeign(['falta_aluno_id']);
        });
    }
}
