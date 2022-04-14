<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarProjetoAlunoTable extends Migration
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
                CREATE TABLE pmieducar.projeto_aluno (
                    ref_cod_projeto integer NOT NULL,
                    ref_cod_aluno integer NOT NULL,
                    data_inclusao date,
                    data_desligamento date,
                    turno integer
                );

                ALTER TABLE ONLY pmieducar.projeto_aluno
                    ADD CONSTRAINT pmieducar_projeto_aluno_pk PRIMARY KEY (ref_cod_projeto, ref_cod_aluno);
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
        Schema::dropIfExists('pmieducar.projeto_aluno');
    }
}
