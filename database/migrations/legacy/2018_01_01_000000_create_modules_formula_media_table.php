<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesFormulaMediaTable extends Migration
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
                
                CREATE TABLE modules.formula_media (
                    id integer NOT NULL,
                    instituicao_id integer NOT NULL,
                    nome character varying(50) NOT NULL,
                    formula_media character varying(200) NOT NULL,
                    tipo_formula smallint DEFAULT 1,
                    substitui_menor_nota_rc smallint DEFAULT 0 NOT NULL
                );

                -- ALTER SEQUENCE modules.formula_media_id_seq OWNED BY modules.formula_media.id;
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
        Schema::dropIfExists('modules.formula_media');
    }
}
