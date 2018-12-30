<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesDocenteLicenciaturaTable extends Migration
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

                CREATE TABLE modules.docente_licenciatura (
                    id integer NOT NULL,
                    servidor_id integer NOT NULL,
                    licenciatura integer NOT NULL,
                    curso_id integer,
                    ano_conclusao integer NOT NULL,
                    ies_id integer,
                    user_id integer NOT NULL,
                    created_at timestamp without time zone NOT NULL,
                    updated_at timestamp without time zone
                );

                -- ALTER SEQUENCE modules.docente_licenciatura_id_seq OWNED BY modules.docente_licenciatura.id;
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
        Schema::dropIfExists('modules.docente_licenciatura');
    }
}
