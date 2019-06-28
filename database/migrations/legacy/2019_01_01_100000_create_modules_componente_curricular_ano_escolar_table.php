<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesComponenteCurricularAnoEscolarTable extends Migration
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

                CREATE TABLE modules.componente_curricular_ano_escolar (
                    componente_curricular_id integer NOT NULL,
                    ano_escolar_id integer NOT NULL,
                    carga_horaria numeric(7,3),
                    tipo_nota integer,
                    anos_letivos smallint[] DEFAULT \'{}\'::smallint[] NOT NULL,
	                updated_at timestamp NULL DEFAULT now()
                );

                ALTER TABLE ONLY modules.componente_curricular_ano_escolar
                    ADD CONSTRAINT componente_curricular_ano_escolar_pkey PRIMARY KEY (componente_curricular_id, ano_escolar_id);
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
        Schema::dropIfExists('modules.componente_curricular_ano_escolar');
    }
}
