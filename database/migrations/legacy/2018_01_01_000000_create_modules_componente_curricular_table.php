<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesComponenteCurricularTable extends Migration
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

                CREATE TABLE modules.componente_curricular (
                    id integer NOT NULL,
                    instituicao_id integer NOT NULL,
                    area_conhecimento_id integer NOT NULL,
                    nome character varying(500) NOT NULL,
                    abreviatura character varying(25) NOT NULL,
                    tipo_base smallint NOT NULL,
                    codigo_educacenso smallint,
                    ordenamento integer DEFAULT 99999
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
        Schema::dropIfExists('modules.componente_curricular');
    }
}
