<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

                ALTER TABLE ONLY pmieducar.quadro_horario_horarios
                    ADD CONSTRAINT quadro_horario_horarios_pkey PRIMARY KEY (ref_cod_quadro_horario, sequencial);

                CREATE INDEX quadro_horario_horarios_busca_horarios_idx ON pmieducar.quadro_horario_horarios USING btree (ref_servidor, ref_cod_instituicao_servidor, dia_semana, hora_inicial, hora_final, ativo);
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
