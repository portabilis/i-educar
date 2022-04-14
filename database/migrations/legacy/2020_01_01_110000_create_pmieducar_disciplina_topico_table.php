<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarDisciplinaTopicoTable extends Migration
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
                CREATE SEQUENCE pmieducar.disciplina_topico_cod_disciplina_topico_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.disciplina_topico (
                    cod_disciplina_topico integer DEFAULT nextval(\'pmieducar.disciplina_topico_cod_disciplina_topico_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_topico character varying(255) NOT NULL,
                    desc_topico text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );

                ALTER TABLE ONLY pmieducar.disciplina_topico
                    ADD CONSTRAINT disciplina_topico_pkey PRIMARY KEY (cod_disciplina_topico);

                CREATE INDEX i_disciplina_topico_ativo ON pmieducar.disciplina_topico USING btree (ativo);

                CREATE INDEX i_disciplina_topico_nm_topico ON pmieducar.disciplina_topico USING btree (nm_topico);

                CREATE INDEX i_disciplina_topico_ref_usuario_cad ON pmieducar.disciplina_topico USING btree (ref_usuario_cad);

                SELECT pg_catalog.setval(\'pmieducar.disciplina_topico_cod_disciplina_topico_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.disciplina_topico');

        DB::unprepared('DROP SEQUENCE pmieducar.disciplina_topico_cod_disciplina_topico_seq;');
    }
}
