<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesConfigMovimentoGeralTable extends Migration
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

                CREATE TABLE modules.config_movimento_geral (
                    id integer NOT NULL,
                    ref_cod_serie integer NOT NULL,
                    coluna integer NOT NULL
                );

                -- ALTER SEQUENCE modules.config_movimento_geral_id_seq OWNED BY modules.config_movimento_geral.id;
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
        Schema::dropIfExists('modules.config_movimento_geral');
    }
}
