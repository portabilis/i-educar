<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
                CREATE TABLE pmieducar.calendario_dia_anotacao (
                    ref_dia integer NOT NULL,
                    ref_mes integer NOT NULL,
                    ref_ref_cod_calendario_ano_letivo integer NOT NULL,
                    ref_cod_calendario_anotacao integer NOT NULL
                );

                ALTER TABLE ONLY pmieducar.calendario_dia_anotacao
                    ADD CONSTRAINT calendario_dia_anotacao_pkey PRIMARY KEY (ref_dia, ref_mes, ref_ref_cod_calendario_ano_letivo, ref_cod_calendario_anotacao);
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
