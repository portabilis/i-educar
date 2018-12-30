<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesParecerGeralTable extends Migration
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
                
                CREATE TABLE modules.parecer_geral (
                    id integer NOT NULL,
                    parecer_aluno_id integer NOT NULL,
                    parecer text,
                    etapa character varying(2) NOT NULL
                );

                -- ALTER SEQUENCE modules.parecer_geral_id_seq OWNED BY modules.parecer_geral.id;
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
        Schema::dropIfExists('modules.parecer_geral');
    }
}
