<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateModulesRegraAvaliacaoSerieAnoTable extends Migration
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
                CREATE TABLE modules.regra_avaliacao_serie_ano (
                    serie_id integer NOT NULL,
                    regra_avaliacao_id integer NOT NULL,
                    regra_avaliacao_diferenciada_id integer,
                    ano_letivo smallint NOT NULL,
	                updated_at timestamp NOT NULL DEFAULT now()
                );

                ALTER TABLE ONLY modules.regra_avaliacao_serie_ano
                    ADD CONSTRAINT regra_avaliacao_serie_ano_pkey PRIMARY KEY (serie_id, ano_letivo);
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
        Schema::dropIfExists('modules.regra_avaliacao_serie_ano');
    }
}
