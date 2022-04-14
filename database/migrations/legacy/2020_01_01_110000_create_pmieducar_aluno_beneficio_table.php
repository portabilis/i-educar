<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarAlunoBeneficioTable extends Migration
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
                CREATE SEQUENCE pmieducar.aluno_beneficio_cod_aluno_beneficio_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.aluno_beneficio (
                    cod_aluno_beneficio integer DEFAULT nextval(\'pmieducar.aluno_beneficio_cod_aluno_beneficio_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_beneficio character varying(255) NOT NULL,
                    desc_beneficio text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );

                ALTER TABLE ONLY pmieducar.aluno_beneficio
                    ADD CONSTRAINT aluno_beneficio_pkey PRIMARY KEY (cod_aluno_beneficio);

                CREATE INDEX i_aluno_beneficio_ativo ON pmieducar.aluno_beneficio USING btree (ativo);

                CREATE INDEX i_aluno_beneficio_nm_beneficio ON pmieducar.aluno_beneficio USING btree (nm_beneficio);

                CREATE INDEX i_aluno_beneficio_ref_usuario_cad ON pmieducar.aluno_beneficio USING btree (ref_usuario_cad);

                SELECT pg_catalog.setval(\'pmieducar.aluno_beneficio_cod_aluno_beneficio_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.aluno_beneficio');

        DB::unprepared('DROP SEQUENCE pmieducar.aluno_beneficio_cod_aluno_beneficio_seq;');
    }
}
