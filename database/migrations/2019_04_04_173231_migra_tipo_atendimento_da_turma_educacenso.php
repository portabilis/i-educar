<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigraTipoAtendimentoDaTurmaEducacenso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('pmieducar.turma')
            ->where('tipo_atendimento', 2)
            ->update([
                'tipo_atendimento' => null,
                'local_funcionamento_diferenciado' => 2,
            ]);

        DB::table('pmieducar.turma')
            ->where('tipo_atendimento', 3)
            ->update([
                'tipo_atendimento' => null,
                'local_funcionamento_diferenciado' => 3,
            ]);

        DB::table('pmieducar.turma')
            ->where('tipo_atendimento', 1)
            ->update([
                'tipo_atendimento' => null,
            ]);
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
