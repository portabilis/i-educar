<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmicontrolesisPortaisTable extends Migration
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
                
                CREATE TABLE pmicontrolesis.portais (
                    cod_portais integer DEFAULT nextval(\'pmicontrolesis.portais_cod_portais_seq\'::regclass) NOT NULL,
                    ref_cod_funcionario_cad integer NOT NULL,
                    ref_cod_funcionario_exc integer,
                    url character varying(255),
                    caminho character varying(255),
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint,
                    title character varying(255),
                    descricao text
                );
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
        Schema::dropIfExists('pmicontrolesis.portais');
    }
}
