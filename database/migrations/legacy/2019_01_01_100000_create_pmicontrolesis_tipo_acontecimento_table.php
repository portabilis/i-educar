<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmicontrolesisTipoAcontecimentoTable extends Migration
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
                
                CREATE SEQUENCE pmicontrolesis.tipo_acontecimento_cod_tipo_acontecimento_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmicontrolesis.tipo_acontecimento (
                    cod_tipo_acontecimento integer DEFAULT nextval(\'pmicontrolesis.tipo_acontecimento_cod_tipo_acontecimento_seq\'::regclass) NOT NULL,
                    ref_cod_funcionario_cad integer NOT NULL,
                    ref_cod_funcionario_exc integer,
                    nm_tipo character varying(255),
                    caminho character varying(255),
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint
                );
                
                ALTER TABLE ONLY pmicontrolesis.tipo_acontecimento
                    ADD CONSTRAINT tipo_acontecimento_pkey PRIMARY KEY (cod_tipo_acontecimento);

                SELECT pg_catalog.setval(\'pmicontrolesis.tipo_acontecimento_cod_tipo_acontecimento_seq\', 1, false);
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
        Schema::dropIfExists('pmicontrolesis.tipo_acontecimento');

        DB::unprepared('DROP SEQUENCE pmicontrolesis.tipo_acontecimento_cod_tipo_acontecimento_seq;');
    }
}
