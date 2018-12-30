<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarFaltaAlunoTable extends Migration
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

                CREATE SEQUENCE pmieducar.falta_aluno_cod_falta_aluno_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.falta_aluno (
                    cod_falta_aluno integer DEFAULT nextval(\'pmieducar.falta_aluno_cod_falta_aluno_seq\'::regclass) NOT NULL,
                    ref_cod_disciplina integer,
                    ref_cod_escola integer,
                    ref_cod_serie integer,
                    ref_cod_matricula integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    faltas integer NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    modulo smallint NOT NULL,
                    ref_cod_curso_disciplina integer
                );
                
                ALTER TABLE ONLY pmieducar.falta_aluno
                    ADD CONSTRAINT falta_aluno_pkey PRIMARY KEY (cod_falta_aluno);

                SELECT pg_catalog.setval(\'pmieducar.falta_aluno_cod_falta_aluno_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.falta_aluno');

        DB::unprepared('DROP SEQUENCE pmieducar.falta_aluno_cod_falta_aluno_seq;');
    }
}
