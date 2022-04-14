<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateModulesParecerComponenteCurricularTable extends Migration
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
                CREATE SEQUENCE modules.parecer_componente_curricular_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.parecer_componente_curricular (
                    id integer NOT NULL,
                    parecer_aluno_id integer NOT NULL,
                    componente_curricular_id integer NOT NULL,
                    parecer text,
                    etapa character varying(2) NOT NULL
                );

                ALTER SEQUENCE modules.parecer_componente_curricular_id_seq OWNED BY modules.parecer_componente_curricular.id;

                ALTER TABLE ONLY modules.parecer_componente_curricular
                    ADD CONSTRAINT parecer_componente_curricular_pkey PRIMARY KEY (parecer_aluno_id, componente_curricular_id, etapa);

                ALTER TABLE ONLY modules.parecer_componente_curricular ALTER COLUMN id SET DEFAULT nextval(\'modules.parecer_componente_curricular_id_seq\'::regclass);

                CREATE UNIQUE INDEX alunocomponenteetapa ON modules.parecer_componente_curricular USING btree (parecer_aluno_id, componente_curricular_id, etapa);

                SELECT pg_catalog.setval(\'modules.parecer_componente_curricular_id_seq\', 1, false);
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
        Schema::dropIfExists('modules.parecer_componente_curricular');
    }
}
