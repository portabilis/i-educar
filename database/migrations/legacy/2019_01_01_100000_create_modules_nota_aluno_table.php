<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesNotaAlunoTable extends Migration
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
                
                CREATE SEQUENCE modules.nota_aluno_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.nota_aluno (
                    id integer NOT NULL,
                    matricula_id integer NOT NULL
                );

                ALTER SEQUENCE modules.nota_aluno_id_seq OWNED BY modules.nota_aluno.id;
                
                ALTER TABLE ONLY modules.nota_aluno
                    ADD CONSTRAINT nota_aluno_pkey PRIMARY KEY (id);

                ALTER TABLE ONLY modules.nota_aluno
                    ADD CONSTRAINT modules_nota_aluno_matricula_id_unique UNIQUE (matricula_id);

                ALTER TABLE ONLY modules.nota_aluno ALTER COLUMN id SET DEFAULT nextval(\'modules.nota_aluno_id_seq\'::regclass);
                
                CREATE INDEX idx_nota_aluno_matricula ON modules.nota_aluno USING btree (matricula_id);

                CREATE INDEX idx_nota_aluno_matricula_id ON modules.nota_aluno USING btree (id, matricula_id);

                SELECT pg_catalog.setval(\'modules.nota_aluno_id_seq\', 2, true);
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
        Schema::dropIfExists('modules.nota_aluno');
    }
}
