<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarAlunoAlunoBeneficioTable extends Migration
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
                CREATE TABLE pmieducar.aluno_aluno_beneficio (
                    aluno_id integer NOT NULL,
                    aluno_beneficio_id integer NOT NULL
                );

                ALTER TABLE ONLY pmieducar.aluno_aluno_beneficio
                    ADD CONSTRAINT aluno_aluno_beneficio_pk PRIMARY KEY (aluno_id, aluno_beneficio_id);
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
        Schema::dropIfExists('pmieducar.aluno_aluno_beneficio');
    }
}
