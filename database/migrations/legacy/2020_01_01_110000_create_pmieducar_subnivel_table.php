<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarSubnivelTable extends Migration
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
                CREATE SEQUENCE pmieducar.subnivel_cod_subnivel_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.subnivel (
                    cod_subnivel integer DEFAULT nextval(\'pmieducar.subnivel_cod_subnivel_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_subnivel_anterior integer,
                    ref_cod_nivel integer NOT NULL,
                    nm_subnivel character varying(100),
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo boolean DEFAULT true NOT NULL,
                    salario double precision NOT NULL
                );

                ALTER TABLE ONLY pmieducar.subnivel
                    ADD CONSTRAINT subnivel_pkey PRIMARY KEY (cod_subnivel);

                SELECT pg_catalog.setval(\'pmieducar.subnivel_cod_subnivel_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.subnivel');

        DB::unprepared('DROP SEQUENCE pmieducar.subnivel_cod_subnivel_seq;');
    }
}
