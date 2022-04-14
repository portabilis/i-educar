<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarMotivoBaixaTable extends Migration
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
                CREATE SEQUENCE pmieducar.motivo_baixa_cod_motivo_baixa_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.motivo_baixa (
                    cod_motivo_baixa integer DEFAULT nextval(\'pmieducar.motivo_baixa_cod_motivo_baixa_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_motivo_baixa character varying(255) NOT NULL,
                    descricao text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_biblioteca integer
                );

                ALTER TABLE ONLY pmieducar.motivo_baixa
                    ADD CONSTRAINT motivo_baixa_pkey PRIMARY KEY (cod_motivo_baixa);

                SELECT pg_catalog.setval(\'pmieducar.motivo_baixa_cod_motivo_baixa_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.motivo_baixa');

        DB::unprepared('DROP SEQUENCE pmieducar.motivo_baixa_cod_motivo_baixa_seq;');
    }
}
