<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuantidadeProfissionaisCadastroEscolas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.escola', function (Blueprint $table) {
            $table->integer('qtd_secretario_escolar')->nullable();
            $table->integer('qtd_auxiliar_administrativo')->nullable();
            $table->integer('qtd_apoio_pedagogico')->nullable();
            $table->integer('qtd_coordenador_turno')->nullable();
            $table->integer('qtd_tecnicos')->nullable();
            $table->integer('qtd_bibliotecarios')->nullable();
            $table->integer('qtd_segurancas')->nullable();
            $table->integer('qtd_auxiliar_servicos_gerais')->nullable();
            $table->integer('qtd_nutricionistas')->nullable();
            $table->integer('qtd_profissionais_preparacao')->nullable();
            $table->integer('qtd_bombeiro')->nullable();
            $table->integer('qtd_psicologo')->nullable();
            $table->integer('qtd_fonoaudiologo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.escola', function (Blueprint $table) {
            $table->dropColumn('qtd_secretario_escolar');
            $table->dropColumn('qtd_auxiliar_administrativo');
            $table->dropColumn('qtd_apoio_pedagogico');
            $table->dropColumn('qtd_coordenador_turno');
            $table->dropColumn('qtd_tecnicos');
            $table->dropColumn('qtd_bibliotecarios');
            $table->dropColumn('qtd_segurancas');
            $table->dropColumn('qtd_auxiliar_servicos_gerais');
            $table->dropColumn('qtd_nutricionistas');
            $table->dropColumn('qtd_profissionais_preparacao');
            $table->dropColumn('qtd_bombeiro');
            $table->dropColumn('qtd_psicologo');
            $table->dropColumn('qtd_fonoaudiologo');
        });
    }
}
