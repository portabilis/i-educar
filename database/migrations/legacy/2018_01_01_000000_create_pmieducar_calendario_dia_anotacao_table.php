<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarCalendarioDiaAnotacaoTable extends Migration
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
                
                CREATE TABLE pmieducar.calendario_dia_anotacao (
                    ref_dia integer NOT NULL,
                    ref_mes integer NOT NULL,
                    ref_ref_cod_calendario_ano_letivo integer NOT NULL,
                    ref_cod_calendario_anotacao integer NOT NULL
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
        Schema::dropIfExists('pmieducar.calendario_dia_anotacao');
    }
}
