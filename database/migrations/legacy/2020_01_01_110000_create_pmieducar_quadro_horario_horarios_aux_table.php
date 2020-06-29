<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarQuadroHorarioHorariosAuxTable extends Migration
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

                CREATE TABLE pmieducar.quadro_horario_horarios_aux (
                    ref_cod_quadro_horario integer NOT NULL,
                    sequencial integer NOT NULL,
                    ref_cod_disciplina integer NOT NULL,
                    ref_cod_escola integer NOT NULL,
                    ref_cod_serie integer NOT NULL,
                    ref_cod_instituicao_servidor integer NOT NULL,
                    ref_servidor integer NOT NULL,
                    dia_semana integer NOT NULL,
                    hora_inicial time without time zone NOT NULL,
                    hora_final time without time zone NOT NULL,
                    identificador character varying(30),
                    data_cadastro timestamp without time zone NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.quadro_horario_horarios_aux
                    ADD CONSTRAINT quadro_horario_horarios_aux_pkey PRIMARY KEY (ref_cod_quadro_horario, sequencial);
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
        Schema::dropIfExists('pmieducar.quadro_horario_horarios_aux');
    }
}
