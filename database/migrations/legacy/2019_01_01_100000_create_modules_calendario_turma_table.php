<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesCalendarioTurmaTable extends Migration
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

                CREATE TABLE modules.calendario_turma (
                    calendario_ano_letivo_id integer NOT NULL,
                    ano integer NOT NULL,
                    mes integer NOT NULL,
                    dia integer NOT NULL,
                    turma_id integer NOT NULL
                );
                
                ALTER TABLE ONLY modules.calendario_turma
                    ADD CONSTRAINT calendario_turma_pk PRIMARY KEY (calendario_ano_letivo_id, ano, mes, dia, turma_id);
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
        Schema::dropIfExists('modules.calendario_turma');
    }
}
