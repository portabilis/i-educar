<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarTipoAvaliacaoTable extends Migration
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

                CREATE SEQUENCE pmieducar.tipo_avaliacao_cod_tipo_avaliacao_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.tipo_avaliacao (
                    cod_tipo_avaliacao integer DEFAULT nextval(\'pmieducar.tipo_avaliacao_cod_tipo_avaliacao_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_tipo character varying(255) NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    conceitual smallint DEFAULT 1,
                    ref_cod_instituicao integer NOT NULL
                );

                ALTER TABLE ONLY pmieducar.tipo_avaliacao
                    ADD CONSTRAINT tipo_avaliacao_pkey PRIMARY KEY (cod_tipo_avaliacao);

                SELECT pg_catalog.setval(\'pmieducar.tipo_avaliacao_cod_tipo_avaliacao_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.tipo_avaliacao');

        DB::unprepared('DROP SEQUENCE pmieducar.tipo_avaliacao_cod_tipo_avaliacao_seq;');
    }
}
