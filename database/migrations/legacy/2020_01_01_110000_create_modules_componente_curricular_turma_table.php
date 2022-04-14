<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateModulesComponenteCurricularTurmaTable extends Migration
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
                CREATE TABLE modules.componente_curricular_turma (
                    componente_curricular_id integer NOT NULL,
                    ano_escolar_id integer NOT NULL,
                    escola_id integer NOT NULL,
                    turma_id integer NOT NULL,
                    carga_horaria numeric(7,3),
                    docente_vinculado smallint,
                    etapas_especificas smallint,
                    etapas_utilizadas character varying,
                    updated_at timestamp without time zone DEFAULT now() NOT NULL
                );

                ALTER TABLE ONLY modules.componente_curricular_turma
                    ADD CONSTRAINT componente_curricular_turma_pkey PRIMARY KEY (componente_curricular_id, turma_id);

                CREATE INDEX componente_curricular_turma_turma_idx ON modules.componente_curricular_turma USING btree (turma_id);
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
        Schema::dropIfExists('modules.componente_curricular_turma');
    }
}
