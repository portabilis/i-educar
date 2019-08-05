<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarHabilitacaoTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.habilitacao_cod_habilitacao_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.habilitacao (
                    cod_habilitacao integer DEFAULT nextval(\'pmieducar.habilitacao_cod_habilitacao_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_tipo character varying(255) NOT NULL,
                    descricao text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_instituicao integer NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.habilitacao
                    ADD CONSTRAINT habilitacao_pkey PRIMARY KEY (cod_habilitacao);

                CREATE INDEX i_habilitacao_ativo ON pmieducar.habilitacao USING btree (ativo);

                CREATE INDEX i_habilitacao_nm_tipo ON pmieducar.habilitacao USING btree (nm_tipo);

                CREATE INDEX i_habilitacao_ref_usuario_cad ON pmieducar.habilitacao USING btree (ref_usuario_cad);

                SELECT pg_catalog.setval(\'pmieducar.habilitacao_cod_habilitacao_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.habilitacao');

        DB::unprepared('DROP SEQUENCE pmieducar.habilitacao_cod_habilitacao_seq;');
    }
}
