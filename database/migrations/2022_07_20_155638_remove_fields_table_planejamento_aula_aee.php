<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFieldsTablePlanejamentoAulaAee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.planejamento_aula_aee', function (Blueprint $table) {
            $table->dropColumn(['caracterizacao_pedagogica']);
            $table->dropColumn(['necessidade_aprendizagem']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.planejamento_aula_aee', function (Blueprint $table) {
            $table->text('caracterizacao_pedagogica')->nullable();
            $table->text('necessidade_aprendizagem')->nullable();
        });
    }
}
