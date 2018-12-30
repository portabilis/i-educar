<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarAnoLetivoModuloTable extends Migration
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

                CREATE TABLE pmieducar.ano_letivo_modulo (
                    ref_ano integer NOT NULL,
                    ref_ref_cod_escola integer NOT NULL,
                    sequencial integer NOT NULL,
                    ref_cod_modulo integer NOT NULL,
                    data_inicio date NOT NULL,
                    data_fim date NOT NULL,
                    dias_letivos numeric(5,0)
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
        Schema::dropIfExists('pmieducar.ano_letivo_modulo');
    }
}
