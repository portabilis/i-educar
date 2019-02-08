<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarEscolaSerieDisciplinaTable extends Migration
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

                CREATE TABLE pmieducar.escola_serie_disciplina (
                    ref_ref_cod_serie integer NOT NULL,
                    ref_ref_cod_escola integer NOT NULL,
                    ref_cod_disciplina integer NOT NULL,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    carga_horaria numeric(7,3),
                    etapas_especificas smallint,
                    etapas_utilizadas character varying,
                    updated_at timestamp without time zone DEFAULT now() NOT NULL,
                    anos_letivos smallint[] DEFAULT \'{}\'::smallint[] NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.escola_serie_disciplina
                    ADD CONSTRAINT escola_serie_disciplina_pkey PRIMARY KEY (ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina);
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
        Schema::dropIfExists('pmieducar.escola_serie_disciplina');
    }
}
