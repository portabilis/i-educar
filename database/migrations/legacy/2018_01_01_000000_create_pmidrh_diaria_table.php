<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmidrhDiariaTable extends Migration
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
                
                CREATE TABLE pmidrh.diaria (
                    cod_diaria integer DEFAULT nextval(\'pmidrh.diaria_cod_diaria_seq\'::regclass) NOT NULL,
                    ref_funcionario_cadastro integer NOT NULL,
                    ref_cod_diaria_grupo integer NOT NULL,
                    ref_funcionario integer NOT NULL,
                    conta_corrente integer,
                    agencia integer,
                    banco integer,
                    dotacao_orcamentaria character varying(50),
                    objetivo text,
                    data_partida timestamp without time zone,
                    data_chegada timestamp without time zone,
                    estadual smallint,
                    destino character varying(100),
                    data_pedido timestamp without time zone,
                    vl100 double precision,
                    vl75 double precision,
                    vl50 double precision,
                    vl25 double precision,
                    roteiro integer,
                    ativo boolean DEFAULT true,
                    ref_cod_setor integer,
                    num_diaria numeric(6,0)
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
        Schema::dropIfExists('pmidrh.diaria');
    }
}