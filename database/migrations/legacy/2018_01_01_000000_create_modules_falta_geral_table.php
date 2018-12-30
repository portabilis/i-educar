<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesFaltaGeralTable extends Migration
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
                
                CREATE TABLE modules.falta_geral (
                    id integer NOT NULL,
                    falta_aluno_id integer NOT NULL,
                    quantidade integer DEFAULT 0,
                    etapa character varying(2) NOT NULL
                );

                -- ALTER SEQUENCE modules.falta_geral_id_seq OWNED BY modules.falta_geral.id;
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
        Schema::dropIfExists('modules.falta_geral');
    }
}
