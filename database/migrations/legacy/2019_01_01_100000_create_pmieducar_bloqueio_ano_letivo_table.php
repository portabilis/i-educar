<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarBloqueioAnoLetivoTable extends Migration
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
                
                CREATE TABLE pmieducar.bloqueio_ano_letivo (
                    ref_cod_instituicao integer NOT NULL,
                    ref_ano integer NOT NULL,
                    data_inicio date NOT NULL,
                    data_fim date NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.bloqueio_ano_letivo
                    ADD CONSTRAINT pmieducar_bloqueio_ano_letivo_pkey PRIMARY KEY (ref_cod_instituicao, ref_ano);
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
        Schema::dropIfExists('pmieducar.bloqueio_ano_letivo');
    }
}
