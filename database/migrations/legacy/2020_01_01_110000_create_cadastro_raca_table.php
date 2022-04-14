<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCadastroRacaTable extends Migration
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
                CREATE SEQUENCE cadastro.raca_cod_raca_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE cadastro.raca (
                    cod_raca integer DEFAULT nextval(\'cadastro.raca_cod_raca_seq\'::regclass) NOT NULL,
                    idpes_exc integer,
                    idpes_cad integer NOT NULL,
                    nm_raca character varying(50) NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo boolean DEFAULT false,
                    raca_educacenso smallint
                );

                ALTER TABLE ONLY cadastro.raca
                    ADD CONSTRAINT raca_pkey PRIMARY KEY (cod_raca);

                SELECT pg_catalog.setval(\'cadastro.raca_cod_raca_seq\', 1, false);
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
        Schema::dropIfExists('cadastro.raca');

        DB::unprepared('DROP SEQUENCE cadastro.raca_cod_raca_seq;');
    }
}
