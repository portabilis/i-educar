<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmiotopicGruposTable extends Migration
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
                
                CREATE SEQUENCE pmiotopic.grupos_cod_grupos_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmiotopic.grupos (
                    cod_grupos integer DEFAULT nextval(\'pmiotopic.grupos_cod_grupos_seq\'::regclass) NOT NULL,
                    ref_pessoa_exc integer,
                    ref_pessoa_cad integer NOT NULL,
                    nm_grupo character varying(255) NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    atendimento smallint DEFAULT 0 NOT NULL
                );
                
                ALTER TABLE ONLY pmiotopic.grupos
                    ADD CONSTRAINT grupos_pkey PRIMARY KEY (cod_grupos);

                SELECT pg_catalog.setval(\'pmiotopic.grupos_cod_grupos_seq\', 1, false);
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
        Schema::dropIfExists('pmiotopic.grupos');

        DB::unprepared('DROP SEQUENCE pmiotopic.grupos_cod_grupos_seq;');
    }
}
