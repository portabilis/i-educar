<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateSerieciascEscolaLixoTable extends Migration
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
                
                CREATE TABLE serieciasc.escola_lixo (
                    ref_cod_escola integer NOT NULL,
                    coleta integer DEFAULT 0,
                    queima integer DEFAULT 0,
                    outra_area integer DEFAULT 0,
                    recicla integer DEFAULT 0,
                    reutiliza integer DEFAULT 0,
                    enterra integer DEFAULT 0,
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
        Schema::dropIfExists('serieciasc.escola_lixo');
    }
}
