<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarInfraComodoFuncaoTable extends Migration
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
                CREATE SEQUENCE pmieducar.infra_comodo_funcao_cod_infra_comodo_funcao_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.infra_comodo_funcao (
                    cod_infra_comodo_funcao integer DEFAULT nextval(\'pmieducar.infra_comodo_funcao_cod_infra_comodo_funcao_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_funcao character varying(255) NOT NULL,
                    desc_funcao text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_escola integer
                );

                ALTER TABLE ONLY pmieducar.infra_comodo_funcao
                    ADD CONSTRAINT infra_comodo_funcao_pkey PRIMARY KEY (cod_infra_comodo_funcao);

                SELECT pg_catalog.setval(\'pmieducar.infra_comodo_funcao_cod_infra_comodo_funcao_seq\', 1, true);
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
        Schema::dropIfExists('pmieducar.infra_comodo_funcao');

        DB::unprepared('DROP SEQUENCE pmieducar.infra_comodo_funcao_cod_infra_comodo_funcao_seq;');
    }
}
