<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarFonteTable extends Migration
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
                CREATE SEQUENCE pmieducar.fonte_cod_fonte_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.fonte (
                    cod_fonte integer DEFAULT nextval(\'pmieducar.fonte_cod_fonte_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_fonte character varying(255) NOT NULL,
                    descricao text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_biblioteca integer
                );

                ALTER TABLE ONLY pmieducar.fonte
                    ADD CONSTRAINT fonte_pkey PRIMARY KEY (cod_fonte);

                SELECT pg_catalog.setval(\'pmieducar.fonte_cod_fonte_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.fonte');

        DB::unprepared('DROP SEQUENCE pmieducar.fonte_cod_fonte_seq;');
    }
}
