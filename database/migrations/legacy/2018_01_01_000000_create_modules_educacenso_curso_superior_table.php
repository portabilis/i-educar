<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesEducacensoCursoSuperiorTable extends Migration
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

                CREATE TABLE modules.educacenso_curso_superior (
                    id integer NOT NULL,
                    curso_id character varying(100) NOT NULL,
                    nome character varying(255) NOT NULL,
                    classe_id integer NOT NULL,
                    user_id integer NOT NULL,
                    created_at timestamp without time zone NOT NULL,
                    updated_at timestamp without time zone,
                    grau_academico smallint
                );

                -- ALTER SEQUENCE modules.educacenso_curso_superior_id_seq OWNED BY modules.educacenso_curso_superior.id;
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
        Schema::dropIfExists('modules.educacenso_curso_superior');
    }
}
