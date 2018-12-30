<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesParecerComponenteCurricularTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        # FIXME

        DB::unprepared(
            '
                SET default_with_oids = false;
                
                CREATE TABLE modules.parecer_componente_curricular (
                    id integer NOT NULL,
                    parecer_aluno_id integer NOT NULL,
                    componente_curricular_id integer NOT NULL,
                    parecer text,
                    etapa character varying(2) NOT NULL
                );

                -- ALTER SEQUENCE modules.parecer_componente_curricular_id_seq OWNED BY modules.parecer_componente_curricular.id;
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
        Schema::dropIfExists('modules.parecer_componente_curricular');
    }
}
