<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateSerieciascEscolaProjetoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            '
                SET default_with_oids = false;
                
                CREATE TABLE serieciasc.escola_projeto (
                    ref_cod_escola integer NOT NULL,
                    danca integer DEFAULT 0,
                    folclorico integer DEFAULT 0,
                    teatral integer DEFAULT 0,
                    ambiental integer DEFAULT 0,
                    coral integer DEFAULT 0,
                    fanfarra integer DEFAULT 0,
                    artes_plasticas integer DEFAULT 0,
                    integrada integer DEFAULT 0,
                    ambiente_alimentacao integer DEFAULT 0,
                    created_at timestamp without time zone NOT NULL,
                    updated_at timestamp without time zone
                );
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
        Schema::dropIfExists('serieciasc.escola_projeto');
    }
}
