<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesFaltaAlunoTable extends Migration
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
                SET default_with_oids = false;
                
                CREATE SEQUENCE modules.falta_aluno_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.falta_aluno (
                    id integer NOT NULL,
                    matricula_id integer NOT NULL,
                    tipo_falta smallint NOT NULL
                );

                ALTER SEQUENCE modules.falta_aluno_id_seq OWNED BY modules.falta_aluno.id;
                
                ALTER TABLE ONLY modules.falta_aluno
                    ADD CONSTRAINT falta_aluno_pkey PRIMARY KEY (id);

                ALTER TABLE ONLY modules.falta_aluno
                    ADD CONSTRAINT modules_falta_aluno_matricula_id_unique UNIQUE (matricula_id);

                ALTER TABLE ONLY modules.falta_aluno ALTER COLUMN id SET DEFAULT nextval(\'modules.falta_aluno_id_seq\'::regclass);
                
                CREATE INDEX idx_falta_aluno_matricula_id ON modules.falta_aluno USING btree (matricula_id);

                CREATE INDEX idx_falta_aluno_matricula_id_tipo ON modules.falta_aluno USING btree (matricula_id, tipo_falta);

                SELECT pg_catalog.setval(\'modules.falta_aluno_id_seq\', 2, true);
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
        Schema::dropIfExists('modules.falta_aluno');
    }
}
