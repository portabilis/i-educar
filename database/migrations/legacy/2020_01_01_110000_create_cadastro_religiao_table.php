<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCadastroReligiaoTable extends Migration
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
                CREATE SEQUENCE cadastro.religiao_cod_religiao_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE cadastro.religiao (
                    cod_religiao integer DEFAULT nextval(\'cadastro.religiao_cod_religiao_seq\'::regclass) NOT NULL,
                    idpes_exc integer,
                    idpes_cad integer NOT NULL,
                    nm_religiao character varying(50) NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo boolean DEFAULT false
                );

                ALTER TABLE ONLY cadastro.religiao
                    ADD CONSTRAINT religiao_pkey PRIMARY KEY (cod_religiao);

                SELECT pg_catalog.setval(\'cadastro.religiao_cod_religiao_seq\', 1, false);
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
        Schema::dropIfExists('cadastro.religiao');

        DB::unprepared('DROP SEQUENCE cadastro.religiao_cod_religiao_seq;');
    }
}
