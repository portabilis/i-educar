<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesEducacensoIesTable extends Migration
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
                
                CREATE TABLE modules.educacenso_ies (
                    id integer NOT NULL,
                    ies_id integer NOT NULL,
                    nome character varying(255) NOT NULL,
                    dependencia_administrativa_id integer NOT NULL,
                    tipo_instituicao_id integer NOT NULL,
                    uf character(2),
                    user_id integer NOT NULL,
                    created_at timestamp without time zone NOT NULL,
                    updated_at timestamp without time zone
                );

                -- ALTER SEQUENCE modules.educacenso_ies_id_seq OWNED BY modules.educacenso_ies.id;
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
        Schema::dropIfExists('modules.educacenso_ies');
    }
}
