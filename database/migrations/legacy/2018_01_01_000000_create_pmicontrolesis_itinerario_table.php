<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmicontrolesisItinerarioTable extends Migration
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
                
                CREATE SEQUENCE pmicontrolesis.itinerario_cod_itinerario_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmicontrolesis.itinerario (
                    cod_itinerario integer DEFAULT nextval(\'pmicontrolesis.itinerario_cod_itinerario_seq\'::regclass) NOT NULL,
                    ref_funcionario_cad integer NOT NULL,
                    ref_funcionario_exc integer,
                    numero integer,
                    itinerario text,
                    retorno text,
                    horarios text,
                    descricao_horario text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    nome character varying(255) NOT NULL
                );
                
                SELECT pg_catalog.setval(\'pmicontrolesis.itinerario_cod_itinerario_seq\', 1, false);
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
        Schema::dropIfExists('pmicontrolesis.itinerario');
    }
}
