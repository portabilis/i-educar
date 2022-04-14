<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarAlunoHistoricoAlturaPesoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            '
                CREATE TABLE pmieducar.aluno_historico_altura_peso (
                    ref_cod_aluno integer NOT NULL,
                    data_historico date NOT NULL,
                    altura numeric(12,2) NOT NULL,
                    peso numeric(12,2) NOT NULL
                );
            '
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pmieducar.aluno_historico_altura_peso');
    }
}
