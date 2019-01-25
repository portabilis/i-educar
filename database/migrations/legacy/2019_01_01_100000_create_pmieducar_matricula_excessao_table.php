<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarMatriculaExcessaoTable extends Migration
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

                CREATE SEQUENCE pmieducar.matricula_excessao_cod_aluno_excessao_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.matricula_excessao (
                    cod_aluno_excessao integer DEFAULT nextval(\'pmieducar.matricula_excessao_cod_aluno_excessao_seq\'::regclass) NOT NULL,
                    ref_cod_matricula integer NOT NULL,
                    ref_cod_turma integer NOT NULL,
                    ref_sequencial integer NOT NULL,
                    ref_cod_serie integer NOT NULL,
                    ref_cod_escola integer NOT NULL,
                    ref_cod_disciplina integer NOT NULL,
                    reprovado_faltas boolean NOT NULL,
                    precisa_exame boolean NOT NULL,
                    permite_exame boolean
                );
                
                ALTER TABLE ONLY pmieducar.matricula_excessao
                    ADD CONSTRAINT matricula_excessao_pk PRIMARY KEY (cod_aluno_excessao);

                SELECT pg_catalog.setval(\'pmieducar.matricula_excessao_cod_aluno_excessao_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.matricula_excessao');

        DB::unprepared('DROP SEQUENCE pmieducar.matricula_excessao_cod_aluno_excessao_seq;');
    }
}
