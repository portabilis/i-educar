<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarQuantidadeReservaExternaTable extends Migration
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

                CREATE TABLE pmieducar.quantidade_reserva_externa (
                    ref_cod_instituicao integer NOT NULL,
                    ref_cod_escola integer NOT NULL,
                    ref_cod_curso integer NOT NULL,
                    ref_cod_serie integer NOT NULL,
                    ref_turma_turno_id integer NOT NULL,
                    ano integer NOT NULL,
                    qtd_alunos integer NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.quantidade_reserva_externa
                    ADD CONSTRAINT quantidade_reserva_externa_pkey PRIMARY KEY (ref_cod_instituicao, ref_cod_escola, ref_cod_curso, ref_cod_serie, ref_turma_turno_id, ano);
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
        Schema::dropIfExists('pmieducar.quantidade_reserva_externa');
    }
}
