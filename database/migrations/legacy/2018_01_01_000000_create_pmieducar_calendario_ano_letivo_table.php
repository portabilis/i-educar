<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarCalendarioAnoLetivoTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.calendario_ano_letivo_cod_calendario_ano_letivo_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.calendario_ano_letivo (
                    cod_calendario_ano_letivo integer DEFAULT nextval(\'pmieducar.calendario_ano_letivo_cod_calendario_ano_letivo_seq\'::regclass) NOT NULL,
                    ref_cod_escola integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ano integer NOT NULL,
                    data_cadastra timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.calendario_ano_letivo
                    ADD CONSTRAINT calendario_ano_letivo_pkey PRIMARY KEY (cod_calendario_ano_letivo);

                SELECT pg_catalog.setval(\'pmieducar.calendario_ano_letivo_cod_calendario_ano_letivo_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.calendario_ano_letivo');

        DB::unprepared('DROP SEQUENCE pmieducar.calendario_ano_letivo_cod_calendario_ano_letivo_seq;');
    }
}
