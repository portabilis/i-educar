<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
                CREATE TABLE pmieducar.ano_letivo_modulo (
                    ref_ano integer NOT NULL,
                    ref_ref_cod_escola integer NOT NULL,
                    sequencial integer NOT NULL,
                    ref_cod_modulo integer NOT NULL,
                    data_inicio date NOT NULL,
                    data_fim date NOT NULL,
                    dias_letivos numeric(5,0)
                );

                ALTER TABLE ONLY pmieducar.ano_letivo_modulo
                    ADD CONSTRAINT ano_letivo_modulo_pkey PRIMARY KEY (ref_ano, ref_ref_cod_escola, sequencial, ref_cod_modulo);
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
