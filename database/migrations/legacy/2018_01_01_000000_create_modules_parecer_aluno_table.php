<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesParecerAlunoTable extends Migration
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
                
                CREATE TABLE modules.parecer_aluno (
                    id integer NOT NULL,
                    matricula_id integer NOT NULL,
                    parecer_descritivo smallint NOT NULL
                );

                -- ALTER SEQUENCE modules.parecer_aluno_id_seq OWNED BY modules.parecer_aluno.id;
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
        Schema::dropIfExists('modules.parecer_aluno');
    }
}
