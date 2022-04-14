<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarDisciplinaSerieTable extends Migration
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
                CREATE TABLE pmieducar.disciplina_serie (
                    ref_cod_disciplina integer NOT NULL,
                    ref_cod_serie integer NOT NULL,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );

                ALTER TABLE ONLY pmieducar.disciplina_serie
                    ADD CONSTRAINT disciplina_serie_pkey PRIMARY KEY (ref_cod_disciplina, ref_cod_serie);
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
        Schema::dropIfExists('pmieducar.disciplina_serie');
    }
}
