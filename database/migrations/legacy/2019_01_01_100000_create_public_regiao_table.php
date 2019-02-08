<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePublicRegiaoTable extends Migration
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
                
                CREATE SEQUENCE public.regiao_cod_regiao_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE public.regiao (
                    cod_regiao integer DEFAULT nextval(\'public.regiao_cod_regiao_seq\'::regclass) NOT NULL,
                    nm_regiao character varying(100)
                );
                
                ALTER TABLE ONLY public.regiao
                    ADD CONSTRAINT regiao_pkey PRIMARY KEY (cod_regiao);

                SELECT pg_catalog.setval(\'public.regiao_cod_regiao_seq\', 1, false);
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
        Schema::dropIfExists('public.regiao');

        DB::unprepared('DROP SEQUENCE public.regiao_cod_regiao_seq;');
    }
}
