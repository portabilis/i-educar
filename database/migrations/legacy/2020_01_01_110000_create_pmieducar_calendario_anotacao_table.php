<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarCalendarioAnotacaoTable extends Migration
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
                CREATE SEQUENCE pmieducar.calendario_anotacao_cod_calendario_anotacao_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.calendario_anotacao (
                    cod_calendario_anotacao integer DEFAULT nextval(\'pmieducar.calendario_anotacao_cod_calendario_anotacao_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_anotacao character varying(255) NOT NULL,
                    descricao text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint NOT NULL
                );

                ALTER TABLE ONLY pmieducar.calendario_anotacao
                    ADD CONSTRAINT calendario_anotacao_pkey PRIMARY KEY (cod_calendario_anotacao);

                SELECT pg_catalog.setval(\'pmieducar.calendario_anotacao_cod_calendario_anotacao_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.calendario_anotacao');

        DB::unprepared('DROP SEQUENCE pmieducar.calendario_anotacao_cod_calendario_anotacao_seq;');
    }
}
