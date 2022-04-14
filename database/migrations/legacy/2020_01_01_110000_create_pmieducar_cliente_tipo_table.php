<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarClienteTipoTable extends Migration
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
                CREATE SEQUENCE pmieducar.cliente_tipo_cod_cliente_tipo_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.cliente_tipo (
                    cod_cliente_tipo integer DEFAULT nextval(\'pmieducar.cliente_tipo_cod_cliente_tipo_seq\'::regclass) NOT NULL,
                    ref_cod_biblioteca integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_tipo character varying(255) NOT NULL,
                    descricao text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );

                ALTER TABLE ONLY pmieducar.cliente_tipo
                    ADD CONSTRAINT cliente_tipo_pkey PRIMARY KEY (cod_cliente_tipo);

                SELECT pg_catalog.setval(\'pmieducar.cliente_tipo_cod_cliente_tipo_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.cliente_tipo');

        DB::unprepared('DROP SEQUENCE pmieducar.cliente_tipo_cod_cliente_tipo_seq;');
    }
}
