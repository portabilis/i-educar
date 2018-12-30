<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateSerieciascEscolaEnergiaTable extends Migration
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
                
                CREATE TABLE serieciasc.escola_energia (
                    ref_cod_escola integer NOT NULL,
                    rede_publica integer DEFAULT 0,
                    gerador_proprio integer DEFAULT 0,
                    solar integer DEFAULT 0,
                    eolica integer DEFAULT 0,
                    inexistente integer DEFAULT 0,
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
        Schema::dropIfExists('serieciasc.escola_energia');
    }
}
