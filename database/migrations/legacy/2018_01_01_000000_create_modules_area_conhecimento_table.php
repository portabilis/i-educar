<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesAreaConhecimentoTable extends Migration
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

                CREATE TABLE modules.area_conhecimento (
                    id integer NOT NULL,
                    instituicao_id integer NOT NULL,
                    nome character varying(200) NOT NULL,
                    secao character varying(50),
                    ordenamento_ac integer DEFAULT 99999
                );
                
                -- ALTER SEQUENCE modules.area_conhecimento_id_seq OWNED BY modules.area_conhecimento.id;
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
        Schema::dropIfExists('modules.area_conhecimento');
    }
}
