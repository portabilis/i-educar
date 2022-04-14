<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarClienteTable extends Migration
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
                CREATE SEQUENCE pmieducar.cliente_cod_cliente_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.cliente (
                    cod_cliente integer DEFAULT nextval(\'pmieducar.cliente_cod_cliente_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_idpes integer NOT NULL,
                    login integer,
                    senha character varying(255),
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    observacoes text
                );

                ALTER TABLE ONLY pmieducar.cliente
                    ADD CONSTRAINT cliente_login_ukey UNIQUE (login);

                ALTER TABLE ONLY pmieducar.cliente
                    ADD CONSTRAINT cliente_pkey PRIMARY KEY (cod_cliente);

                SELECT pg_catalog.setval(\'pmieducar.cliente_cod_cliente_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.cliente');

        DB::unprepared('DROP SEQUENCE pmieducar.cliente_cod_cliente_seq;');
    }
}
