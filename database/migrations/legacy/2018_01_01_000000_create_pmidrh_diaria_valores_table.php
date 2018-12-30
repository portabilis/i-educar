<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmidrhDiariaValoresTable extends Migration
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
                
                CREATE SEQUENCE pmidrh.diaria_valores_cod_diaria_valores_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmidrh.diaria_valores (
                    cod_diaria_valores integer DEFAULT nextval(\'pmidrh.diaria_valores_cod_diaria_valores_seq\'::regclass) NOT NULL,
                    ref_funcionario_cadastro integer NOT NULL,
                    ref_cod_diaria_grupo integer NOT NULL,
                    estadual smallint NOT NULL,
                    p100 double precision,
                    p75 double precision,
                    p50 double precision,
                    p25 double precision,
                    data_vigencia timestamp without time zone NOT NULL
                );
                
                ALTER TABLE ONLY pmidrh.diaria_valores
                    ADD CONSTRAINT diaria_valores_pkey PRIMARY KEY (cod_diaria_valores);

                SELECT pg_catalog.setval(\'pmidrh.diaria_valores_cod_diaria_valores_seq\', 1, false);
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
        Schema::dropIfExists('pmidrh.diaria_valores');

        DB::unprepared('DROP SEQUENCE pmidrh.diaria_valores_cod_diaria_valores_seq;');
    }
}
