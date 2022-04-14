<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarTransferenciaTipoTable extends Migration
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
                CREATE SEQUENCE pmieducar.transferencia_tipo_cod_transferencia_tipo_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.transferencia_tipo (
                    cod_transferencia_tipo integer DEFAULT nextval(\'pmieducar.transferencia_tipo_cod_transferencia_tipo_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_tipo character varying(255) NOT NULL,
                    desc_tipo text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_instituicao integer
                );

                ALTER TABLE ONLY pmieducar.transferencia_tipo
                    ADD CONSTRAINT transferencia_tipo_pkey PRIMARY KEY (cod_transferencia_tipo);

                SELECT pg_catalog.setval(\'pmieducar.transferencia_tipo_cod_transferencia_tipo_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.transferencia_tipo');

        DB::unprepared('DROP SEQUENCE pmieducar.transferencia_tipo_cod_transferencia_tipo_seq;');
    }
}
