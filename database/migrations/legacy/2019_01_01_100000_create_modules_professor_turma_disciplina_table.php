<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesProfessorTurmaDisciplinaTable extends Migration
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
                
                CREATE TABLE modules.professor_turma_disciplina (
                    professor_turma_id integer NOT NULL,
                    componente_curricular_id integer NOT NULL
                );
                
                ALTER TABLE ONLY modules.professor_turma_disciplina
                    ADD CONSTRAINT professor_turma_disciplina_pk PRIMARY KEY (professor_turma_id, componente_curricular_id);
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
        Schema::dropIfExists('modules.professor_turma_disciplina');
    }
}
