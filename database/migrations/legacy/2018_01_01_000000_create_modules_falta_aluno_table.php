<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesFaltaAlunoTable extends Migration
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
                
                CREATE TABLE modules.falta_aluno (
                    id integer NOT NULL,
                    matricula_id integer NOT NULL,
                    tipo_falta smallint NOT NULL
                );

                -- ALTER SEQUENCE modules.falta_aluno_id_seq OWNED BY modules.falta_aluno.id;
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
        Schema::dropIfExists('modules.falta_aluno');
    }
}
