<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarSerieVagaTable extends Migration
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
                SET default_with_oids = false;

                CREATE TABLE pmieducar.serie_vaga (
                    ano integer NOT NULL,
                    cod_serie_vaga integer NOT NULL,
                    ref_cod_instituicao integer NOT NULL,
                    ref_cod_escola integer NOT NULL,
                    ref_cod_curso integer NOT NULL,
                    ref_cod_serie integer NOT NULL,
                    vagas smallint NOT NULL,
                    turno smallint DEFAULT 1 NOT NULL
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
        Schema::dropIfExists('pmieducar.serie_vaga');
    }
}
