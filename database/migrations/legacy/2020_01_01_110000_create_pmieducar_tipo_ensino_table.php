<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarTipoEnsinoTable extends Migration
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
                CREATE SEQUENCE pmieducar.tipo_ensino_cod_tipo_ensino_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.tipo_ensino (
                    cod_tipo_ensino integer DEFAULT nextval(\'pmieducar.tipo_ensino_cod_tipo_ensino_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_tipo character varying(255) NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_instituicao integer NOT NULL,
                    atividade_complementar boolean DEFAULT false
                );

                ALTER TABLE ONLY pmieducar.tipo_ensino
                    ADD CONSTRAINT tipo_ensino_pkey PRIMARY KEY (cod_tipo_ensino);

                SELECT pg_catalog.setval(\'pmieducar.tipo_ensino_cod_tipo_ensino_seq\', 1, true);
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
        Schema::dropIfExists('pmieducar.tipo_ensino');

        DB::unprepared('DROP SEQUENCE pmieducar.tipo_ensino_cod_tipo_ensino_seq;');
    }
}
