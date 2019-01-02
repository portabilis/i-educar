<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmicontrolesisTelefonesTable extends Migration
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
                
                CREATE SEQUENCE pmicontrolesis.telefones_cod_telefones_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmicontrolesis.telefones (
                    cod_telefones integer DEFAULT nextval(\'pmicontrolesis.telefones_cod_telefones_seq\'::regclass) NOT NULL,
                    ref_funcionario_cad integer NOT NULL,
                    ref_funcionario_exc integer,
                    nome character varying(255) NOT NULL,
                    numero character varying,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );
                
                ALTER TABLE ONLY pmicontrolesis.telefones
                    ADD CONSTRAINT telefones_pkey PRIMARY KEY (cod_telefones);

                SELECT pg_catalog.setval(\'pmicontrolesis.telefones_cod_telefones_seq\', 1, false);
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
        Schema::dropIfExists('pmicontrolesis.telefones');

        DB::unprepared('DROP SEQUENCE pmicontrolesis.telefones_cod_telefones_seq;');
    }
}
