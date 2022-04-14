<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateModulesParecerAlunoTable extends Migration
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
                CREATE SEQUENCE modules.parecer_aluno_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.parecer_aluno (
                    id integer NOT NULL,
                    matricula_id integer NOT NULL,
                    parecer_descritivo smallint NOT NULL
                );

                ALTER SEQUENCE modules.parecer_aluno_id_seq OWNED BY modules.parecer_aluno.id;

                ALTER TABLE ONLY modules.parecer_aluno
                    ADD CONSTRAINT parecer_aluno_pkey PRIMARY KEY (id);

                ALTER TABLE ONLY modules.parecer_aluno
                    ADD CONSTRAINT modules_parecer_aluno_matricula_id_unique UNIQUE (matricula_id);

                ALTER TABLE ONLY modules.parecer_aluno ALTER COLUMN id SET DEFAULT nextval(\'modules.parecer_aluno_id_seq\'::regclass);

                CREATE INDEX idx_parecer_aluno_matricula_id ON modules.parecer_aluno USING btree (matricula_id);

                SELECT pg_catalog.setval(\'modules.parecer_aluno_id_seq\', 1, false);
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
        Schema::dropIfExists('modules.parecer_aluno');
    }
}
