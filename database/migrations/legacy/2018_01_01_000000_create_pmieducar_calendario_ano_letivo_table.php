<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarCalendarioAnoLetivoTable extends Migration
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
                
                CREATE TABLE pmieducar.calendario_ano_letivo (
                    cod_calendario_ano_letivo integer DEFAULT nextval(\'pmieducar.calendario_ano_letivo_cod_calendario_ano_letivo_seq\'::regclass) NOT NULL,
                    ref_cod_escola integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ano integer NOT NULL,
                    data_cadastra timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
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
        Schema::dropIfExists('pmieducar.calendario_ano_letivo');
    }
}
