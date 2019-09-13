<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarFuncaoTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.funcao_cod_funcao_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.funcao (
                    cod_funcao integer DEFAULT nextval(\'pmieducar.funcao_cod_funcao_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_funcao character varying(255) NOT NULL,
                    abreviatura character varying(30) NOT NULL,
                    professor smallint DEFAULT (0)::smallint NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_instituicao integer NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.funcao
                    ADD CONSTRAINT funcao_pkey PRIMARY KEY (cod_funcao);

                CREATE INDEX i_funcao_abreviatura ON pmieducar.funcao USING btree (abreviatura);

                CREATE INDEX i_funcao_ativo ON pmieducar.funcao USING btree (ativo);

                CREATE INDEX i_funcao_nm_funcao ON pmieducar.funcao USING btree (nm_funcao);

                CREATE INDEX i_funcao_professor ON pmieducar.funcao USING btree (professor);

                CREATE INDEX i_funcao_ref_usuario_cad ON pmieducar.funcao USING btree (ref_usuario_cad);

                SELECT pg_catalog.setval(\'pmieducar.funcao_cod_funcao_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.funcao');

        DB::unprepared('DROP SEQUENCE pmieducar.funcao_cod_funcao_seq;');
    }
}
