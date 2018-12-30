<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesNotaComponenteCurricularTable extends Migration
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
                
                CREATE TABLE modules.nota_componente_curricular (
                    id integer NOT NULL,
                    nota_aluno_id integer NOT NULL,
                    componente_curricular_id integer NOT NULL,
                    nota numeric(8,4) DEFAULT 0,
                    nota_arredondada character varying(10) DEFAULT 0,
                    etapa character varying(2) NOT NULL,
                    nota_recuperacao character varying(10),
                    nota_original character varying(10),
                    nota_recuperacao_especifica character varying(10)
                );

                -- ALTER SEQUENCE modules.nota_componente_curricular_id_seq OWNED BY modules.nota_componente_curricular.id;
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
        Schema::dropIfExists('modules.nota_componente_curricular');
    }
}
