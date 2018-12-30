<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarQuadroHorarioHorariosTable extends Migration
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

                CREATE TABLE pmieducar.quadro_horario_horarios (
                    ref_cod_quadro_horario integer NOT NULL,
                    sequencial integer NOT NULL,
                    ref_cod_disciplina integer NOT NULL,
                    ref_cod_escola integer NOT NULL,
                    ref_cod_serie integer NOT NULL,
                    ref_cod_instituicao_substituto integer,
                    ref_cod_instituicao_servidor integer NOT NULL,
                    ref_servidor_substituto integer,
                    ref_servidor integer NOT NULL,
                    dia_semana integer NOT NULL,
                    hora_inicial time without time zone NOT NULL,
                    hora_final time without time zone NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
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
        Schema::dropIfExists('pmieducar.quadro_horario_horarios');
    }
}
