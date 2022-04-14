<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarEscolaLocalizacaoTable extends Migration
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
                CREATE SEQUENCE pmieducar.escola_localizacao_cod_escola_localizacao_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.escola_localizacao (
                    cod_escola_localizacao integer DEFAULT nextval(\'pmieducar.escola_localizacao_cod_escola_localizacao_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_localizacao character varying(255) NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_instituicao integer NOT NULL
                );

                ALTER TABLE ONLY pmieducar.escola_localizacao
                    ADD CONSTRAINT escola_localizacao_pkey PRIMARY KEY (cod_escola_localizacao);

                CREATE INDEX i_escola_localizacao_ativo ON pmieducar.escola_localizacao USING btree (ativo);

                CREATE INDEX i_escola_localizacao_nm_localizacao ON pmieducar.escola_localizacao USING btree (nm_localizacao);

                CREATE INDEX i_escola_localizacao_ref_usuario_cad ON pmieducar.escola_localizacao USING btree (ref_usuario_cad);

                SELECT pg_catalog.setval(\'pmieducar.escola_localizacao_cod_escola_localizacao_seq\', 1, true);
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
        Schema::dropIfExists('pmieducar.escola_localizacao');

        DB::unprepared('DROP SEQUENCE pmieducar.escola_localizacao_cod_escola_localizacao_seq;');
    }
}
