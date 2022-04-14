<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarServidorDisciplinaTable extends Migration
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
                CREATE TABLE pmieducar.servidor_disciplina (
                    ref_cod_disciplina integer NOT NULL,
                    ref_ref_cod_instituicao integer NOT NULL,
                    ref_cod_servidor integer NOT NULL,
                    ref_cod_curso integer NOT NULL
                );

                ALTER TABLE ONLY pmieducar.servidor_disciplina
                    ADD CONSTRAINT servidor_disciplina_pkey PRIMARY KEY (ref_cod_disciplina, ref_ref_cod_instituicao, ref_cod_servidor, ref_cod_curso);
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
        Schema::dropIfExists('pmieducar.servidor_disciplina');
    }
}
