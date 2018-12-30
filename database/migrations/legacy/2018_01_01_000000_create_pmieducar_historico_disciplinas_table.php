<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarHistoricoDisciplinasTable extends Migration
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

                CREATE TABLE pmieducar.historico_disciplinas (
                    sequencial integer NOT NULL,
                    ref_ref_cod_aluno integer NOT NULL,
                    ref_sequencial integer NOT NULL,
                    nm_disciplina text NOT NULL,
                    nota character varying(255) NOT NULL,
                    faltas integer,
                    import numeric(1,0),
                    ordenamento integer,
                    carga_horaria_disciplina integer,
                    dependencia boolean DEFAULT false
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
        Schema::dropIfExists('pmieducar.historico_disciplinas');
    }
}
