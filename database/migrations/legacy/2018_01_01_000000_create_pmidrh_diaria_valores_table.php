<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmidrhDiariaValoresTable extends Migration
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
                
                CREATE TABLE pmidrh.diaria_valores (
                    cod_diaria_valores integer DEFAULT nextval(\'pmidrh.diaria_valores_cod_diaria_valores_seq\'::regclass) NOT NULL,
                    ref_funcionario_cadastro integer NOT NULL,
                    ref_cod_diaria_grupo integer NOT NULL,
                    estadual smallint NOT NULL,
                    p100 double precision,
                    p75 double precision,
                    p50 double precision,
                    p25 double precision,
                    data_vigencia timestamp without time zone NOT NULL
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
        Schema::dropIfExists('pmidrh.diaria_valores');
    }
}
