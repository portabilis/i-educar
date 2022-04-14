<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateModulesEtapasCursoEducacensoTable extends Migration
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
                CREATE TABLE modules.etapas_curso_educacenso (
                    etapa_id integer NOT NULL,
                    curso_id integer NOT NULL
                );

                ALTER TABLE ONLY modules.etapas_curso_educacenso
                    ADD CONSTRAINT etapas_curso_educacenso_pk PRIMARY KEY (etapa_id, curso_id);
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
        Schema::dropIfExists('modules.etapas_curso_educacenso');
    }
}
