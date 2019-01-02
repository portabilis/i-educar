<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarDisciplinaTable extends Migration
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
                SET default_with_oids = true;
                
                CREATE SEQUENCE pmieducar.disciplina_cod_disciplina_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.disciplina (
                    cod_disciplina integer DEFAULT nextval(\'pmieducar.disciplina_cod_disciplina_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    desc_disciplina text,
                    desc_resumida text,
                    abreviatura character varying(15) NOT NULL,
                    carga_horaria integer NOT NULL,
                    apura_falta smallint DEFAULT (0)::smallint NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    nm_disciplina character varying(255) NOT NULL,
                    ref_cod_curso integer
                );
                
                ALTER TABLE ONLY pmieducar.disciplina
                    ADD CONSTRAINT disciplina_pkey PRIMARY KEY (cod_disciplina);

                CREATE INDEX i_disciplina_abreviatura ON pmieducar.disciplina USING btree (abreviatura);

                CREATE INDEX i_disciplina_apura_falta ON pmieducar.disciplina USING btree (apura_falta);

                CREATE INDEX i_disciplina_ativo ON pmieducar.disciplina USING btree (ativo);

                CREATE INDEX i_disciplina_carga_horaria ON pmieducar.disciplina USING btree (carga_horaria);

                CREATE INDEX i_disciplina_nm_disciplina ON pmieducar.disciplina USING btree (nm_disciplina);

                CREATE INDEX i_disciplina_ref_usuario_cad ON pmieducar.disciplina USING btree (ref_usuario_cad);

                SELECT pg_catalog.setval(\'pmieducar.disciplina_cod_disciplina_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.disciplina');

        DB::unprepared('DROP SEQUENCE pmieducar.disciplina_cod_disciplina_seq;');
    }
}
