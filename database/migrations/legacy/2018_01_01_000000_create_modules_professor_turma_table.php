<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesProfessorTurmaTable extends Migration
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
                
                CREATE TABLE modules.professor_turma (
                    id integer DEFAULT nextval(\'modules.professor_turma_id_seq\'::regclass) NOT NULL,
                    ano smallint NOT NULL,
                    instituicao_id integer NOT NULL,
                    turma_id integer NOT NULL,
                    servidor_id integer NOT NULL,
                    funcao_exercida smallint NOT NULL,
                    tipo_vinculo smallint,
                    permite_lancar_faltas_componente integer DEFAULT 0,
                    updated_at timestamp without time zone,
                    turno_id integer
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
        Schema::dropIfExists('modules.professor_turma');
    }
}
