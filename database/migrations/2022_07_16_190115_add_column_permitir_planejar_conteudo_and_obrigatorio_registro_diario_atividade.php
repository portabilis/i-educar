<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPermitirPlanejarConteudoAndObrigatorioRegistroDiarioAtividade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.instituicao', function (Blueprint $table) {
            $table->boolean('permitir_planeja_conteudos')->nullable();
            $table->boolean('obrigatorio_registro_diario_atividade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
