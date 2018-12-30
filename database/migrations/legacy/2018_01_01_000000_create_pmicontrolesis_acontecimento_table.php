<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmicontrolesisAcontecimentoTable extends Migration
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
                
                CREATE SEQUENCE pmicontrolesis.acontecimento_cod_acontecimento_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmicontrolesis.acontecimento (
                    cod_acontecimento integer DEFAULT nextval(\'pmicontrolesis.acontecimento_cod_acontecimento_seq\'::regclass) NOT NULL,
                    ref_cod_tipo_acontecimento integer NOT NULL,
                    ref_cod_funcionario_cad integer NOT NULL,
                    ref_cod_funcionario_exc integer,
                    titulo character varying(255),
                    descricao text,
                    dt_inicio timestamp without time zone,
                    dt_fim timestamp without time zone,
                    hr_inicio time without time zone,
                    hr_fim time without time zone,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint,
                    local character varying,
                    contato character varying,
                    link character varying
                );
                
                SELECT pg_catalog.setval(\'pmicontrolesis.acontecimento_cod_acontecimento_seq\', 1, false);
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
        Schema::dropIfExists('pmicontrolesis.acontecimento');
    }
}
