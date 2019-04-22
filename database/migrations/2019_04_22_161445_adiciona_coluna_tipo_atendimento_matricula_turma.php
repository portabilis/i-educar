<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdicionaColunaTipoAtendimentoMatriculaTurma extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE pmieducar.matricula_turma ADD COLUMN tipo_atendimento integer[]');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.matricula_turma', function (Blueprint $table) {
            $table->dropColumn('tipo_atendimento');
        });
    }
}
