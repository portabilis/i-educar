<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarEscolaRedeEnsinoTable extends Migration
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
                CREATE SEQUENCE pmieducar.escola_rede_ensino_cod_escola_rede_ensino_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.escola_rede_ensino (
                    cod_escola_rede_ensino integer DEFAULT nextval(\'pmieducar.escola_rede_ensino_cod_escola_rede_ensino_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_rede character varying(255) NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_instituicao integer NOT NULL
                );

                ALTER TABLE ONLY pmieducar.escola_rede_ensino
                    ADD CONSTRAINT escola_rede_ensino_pkey PRIMARY KEY (cod_escola_rede_ensino);

                CREATE INDEX i_escola_rede_ensino_ativo ON pmieducar.escola_rede_ensino USING btree (ativo);

                CREATE INDEX i_escola_rede_ensino_nm_rede ON pmieducar.escola_rede_ensino USING btree (nm_rede);

                CREATE INDEX i_escola_rede_ensino_ref_usuario_cad ON pmieducar.escola_rede_ensino USING btree (ref_usuario_cad);

                SELECT pg_catalog.setval(\'pmieducar.escola_rede_ensino_cod_escola_rede_ensino_seq\', 1, true);
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
        Schema::dropIfExists('pmieducar.escola_rede_ensino');

        DB::unprepared('DROP SEQUENCE pmieducar.escola_rede_ensino_cod_escola_rede_ensino_seq;');
    }
}
