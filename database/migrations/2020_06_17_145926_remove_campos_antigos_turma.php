<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveCamposAntigosTurma extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.turma', function (Blueprint $table) {
            $table->dropColumn([
                'turma_mais_educacao',
                'atividade_complementar_1',
                'atividade_complementar_2',
                'atividade_complementar_3',
                'atividade_complementar_4',
                'atividade_complementar_5',
                'atividade_complementar_6',
                'aee_braille',
                'aee_recurso_optico',
                'aee_estrategia_desenvolvimento',
                'aee_tecnica_mobilidade',
                'aee_libras',
                'aee_caa',
                'aee_curricular',
                'aee_soroban',
                'aee_informatica',
                'aee_lingua_escrita',
                'aee_autonomia',
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
        Schema::table('pmieducar.turma', function (Blueprint $table) {
            $table->smallInteger('turma_mais_educacao')->nullable();
            $table->integer('atividade_complementar_1')->nullable();
            $table->integer('atividade_complementar_2')->nullable();
            $table->integer('atividade_complementar_3')->nullable();
            $table->integer('atividade_complementar_4')->nullable();
            $table->integer('atividade_complementar_5')->nullable();
            $table->integer('atividade_complementar_6')->nullable();
            $table->smallInteger('aee_braille')->nullable();
            $table->smallInteger('aee_recurso_optico')->nullable();
            $table->smallInteger('aee_estrategia_desenvolvimento')->nullable();
            $table->smallInteger('aee_tecnica_mobilidade')->nullable();
            $table->smallInteger('aee_libras')->nullable();
            $table->smallInteger('aee_caa')->nullable();
            $table->smallInteger('aee_curricular')->nullable();
            $table->smallInteger('aee_soroban')->nullable();
            $table->smallInteger('aee_informatica')->nullable();
            $table->smallInteger('aee_lingua_escrita')->nullable();
            $table->smallInteger('aee_autonomia')->nullable();
        });
    }
}
