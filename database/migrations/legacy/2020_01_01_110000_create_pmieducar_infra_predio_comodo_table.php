<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarInfraPredioComodoTable extends Migration
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
                CREATE SEQUENCE pmieducar.infra_predio_comodo_cod_infra_predio_comodo_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.infra_predio_comodo (
                    cod_infra_predio_comodo integer DEFAULT nextval(\'pmieducar.infra_predio_comodo_cod_infra_predio_comodo_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_infra_comodo_funcao integer NOT NULL,
                    ref_cod_infra_predio integer NOT NULL,
                    nm_comodo character varying(255) NOT NULL,
                    desc_comodo text,
                    area double precision NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );

                ALTER TABLE ONLY pmieducar.infra_predio_comodo
                    ADD CONSTRAINT infra_predio_comodo_pkey PRIMARY KEY (cod_infra_predio_comodo);

                SELECT pg_catalog.setval(\'pmieducar.infra_predio_comodo_cod_infra_predio_comodo_seq\', 1, true);
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
        Schema::dropIfExists('pmieducar.infra_predio_comodo');

        DB::unprepared('DROP SEQUENCE pmieducar.infra_predio_comodo_cod_infra_predio_comodo_seq;');
    }
}
