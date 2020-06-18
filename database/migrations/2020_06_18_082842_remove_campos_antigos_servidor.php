<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveCamposAntigosServidor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.servidor', function (Blueprint $table) {
            $table->dropColumn([
                'situacao_curso_superior_1',
                'formacao_complementacao_pedagogica_1',
                'codigo_curso_superior_1',
                'ano_inicio_curso_superior_1',
                'ano_conclusao_curso_superior_1',
                'instituicao_curso_superior_1',
                'situacao_curso_superior_2',
                'formacao_complementacao_pedagogica_2',
                'codigo_curso_superior_2',
                'ano_inicio_curso_superior_2',
                'ano_conclusao_curso_superior_2',
                'instituicao_curso_superior_2',
                'situacao_curso_superior_3',
                'formacao_complementacao_pedagogica_3',
                'codigo_curso_superior_3',
                'ano_inicio_curso_superior_3',
                'ano_conclusao_curso_superior_3',
                'instituicao_curso_superior_3',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.servidor', function (Blueprint $table) {
            $table->smallInteger('situacao_curso_superior_1')->nullable();
            $table->smallInteger('formacao_complementacao_pedagogica_1')->nullable();
            $table->integer('codigo_curso_superior_1')->nullable();
            $table->integer('ano_inicio_curso_superior_1')->nullable();
            $table->integer('ano_conclusao_curso_superior_1')->nullable();
            $table->smallInteger('instituicao_curso_superior_1')->nullable();
            $table->smallInteger('situacao_curso_superior_2')->nullable();
            $table->smallInteger('formacao_complementacao_pedagogica_2')->nullable();
            $table->integer('codigo_curso_superior_2')->nullable();
            $table->integer('ano_inicio_curso_superior_2')->nullable();
            $table->integer('ano_conclusao_curso_superior_2')->nullable();
            $table->smallInteger('instituicao_curso_superior_2')->nullable();
            $table->smallInteger('situacao_curso_superior_3')->nullable();
            $table->smallInteger('formacao_complementacao_pedagogica_3')->nullable();
            $table->integer('codigo_curso_superior_3')->nullable();
            $table->integer('ano_inicio_curso_superior_3')->nullable();
            $table->integer('ano_conclusao_curso_superior_3')->nullable();
            $table->smallInteger('instituicao_curso_superior_3')->nullable();
        });
    }
}
