<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
                CREATE TABLE pmieducar.historico_disciplinas (
	                id serial NOT NULL,
                    sequencial integer NOT NULL,
                    ref_ref_cod_aluno integer NOT NULL,
                    ref_sequencial integer NOT NULL,
                    nm_disciplina text NOT NULL,
                    nota character varying(255) NOT NULL,
                    faltas integer,
                    import numeric(1,0),
                    ordenamento integer,
                    carga_horaria_disciplina integer,
                    dependencia boolean DEFAULT false,
                    tipo_base int4 NOT NULL DEFAULT 1
                );

                ALTER TABLE ONLY pmieducar.historico_disciplinas
                    ADD CONSTRAINT historico_disciplinas_pkey PRIMARY KEY (id);
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
