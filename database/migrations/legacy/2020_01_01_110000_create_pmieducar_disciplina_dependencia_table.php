<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarDisciplinaDependenciaTable extends Migration
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
                CREATE TABLE pmieducar.disciplina_dependencia (
                    ref_cod_matricula integer NOT NULL,
                    ref_cod_disciplina integer NOT NULL,
                    ref_cod_escola integer NOT NULL,
                    ref_cod_serie integer NOT NULL,
                    observacao text,
                    cod_disciplina_dependencia integer NOT NULL,
	                updated_at timestamp NULL DEFAULT now()
                );

                ALTER TABLE ONLY pmieducar.disciplina_dependencia
                    ADD CONSTRAINT cod_disciplina_dependencia_pkey PRIMARY KEY (cod_disciplina_dependencia);
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
        Schema::dropIfExists('pmieducar.disciplina_dependencia');
    }
}
