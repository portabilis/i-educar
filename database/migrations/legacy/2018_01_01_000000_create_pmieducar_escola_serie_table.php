<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarEscolaSerieTable extends Migration
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

                CREATE TABLE pmieducar.escola_serie (
                    ref_cod_escola integer NOT NULL,
                    ref_cod_serie integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    hora_inicial time without time zone,
                    hora_final time without time zone,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    hora_inicio_intervalo time without time zone,
                    hora_fim_intervalo time without time zone,
                    bloquear_enturmacao_sem_vagas integer,
                    bloquear_cadastro_turma_para_serie_com_vagas integer,
                    anos_letivos smallint[] DEFAULT \'{}\'::smallint[] NOT NULL
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
        Schema::dropIfExists('pmieducar.escola_serie');
    }
}
