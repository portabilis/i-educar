<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarInfraPredioTable extends Migration
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
                SET default_with_oids = true;
                
                CREATE SEQUENCE pmieducar.infra_predio_cod_infra_predio_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.infra_predio (
                    cod_infra_predio integer DEFAULT nextval(\'pmieducar.infra_predio_cod_infra_predio_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_escola integer NOT NULL,
                    nm_predio character varying(255) NOT NULL,
                    desc_predio text,
                    endereco text NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.infra_predio
                    ADD CONSTRAINT infra_predio_pkey PRIMARY KEY (cod_infra_predio);

                SELECT pg_catalog.setval(\'pmieducar.infra_predio_cod_infra_predio_seq\', 1, true);
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
        Schema::dropIfExists('pmieducar.infra_predio');

        DB::unprepared('DROP SEQUENCE pmieducar.infra_predio_cod_infra_predio_seq;');
    }
}
