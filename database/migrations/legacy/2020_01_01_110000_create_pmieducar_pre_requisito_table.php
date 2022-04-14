<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarPreRequisitoTable extends Migration
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
                CREATE SEQUENCE pmieducar.pre_requisito_cod_pre_requisito_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.pre_requisito (
                    cod_pre_requisito integer DEFAULT nextval(\'pmieducar.pre_requisito_cod_pre_requisito_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    schema_ character varying(50) NOT NULL,
                    tabela character varying(50) NOT NULL,
                    nome character varying(50) NOT NULL,
                    sql text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );

                ALTER TABLE ONLY pmieducar.pre_requisito
                    ADD CONSTRAINT pre_requisito_pkey PRIMARY KEY (cod_pre_requisito);

                SELECT pg_catalog.setval(\'pmieducar.pre_requisito_cod_pre_requisito_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.pre_requisito');

        DB::unprepared('DROP SEQUENCE pmieducar.pre_requisito_cod_pre_requisito_seq;');
    }
}
