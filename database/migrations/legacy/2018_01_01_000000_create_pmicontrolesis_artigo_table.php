<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmicontrolesisArtigoTable extends Migration
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
                
                CREATE SEQUENCE pmicontrolesis.artigo_cod_artigo_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmicontrolesis.artigo (
                    cod_artigo integer DEFAULT nextval(\'pmicontrolesis.artigo_cod_artigo_seq\'::regclass) NOT NULL,
                    texto text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint
                );
                
                ALTER TABLE ONLY pmicontrolesis.artigo
                    ADD CONSTRAINT artigo_pkey PRIMARY KEY (cod_artigo);

                SELECT pg_catalog.setval(\'pmicontrolesis.artigo_cod_artigo_seq\', 1, false);
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
        Schema::dropIfExists('pmicontrolesis.artigo');

        DB::unprepared('DROP SEQUENCE pmicontrolesis.artigo_cod_artigo_seq;');
    }
}
