<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateSerieciascEscolaLinguaIndigenaTable extends Migration
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
                
                CREATE TABLE serieciasc.escola_lingua_indigena (
                    ref_cod_escola integer NOT NULL,
                    educacao_indigena integer DEFAULT 0,
                    lingua_indigena integer DEFAULT 0,
                    lingua_portuguesa integer DEFAULT 0,
                    materiais_especificos integer DEFAULT 0,
                    ue_terra_indigena integer DEFAULT 0,
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
        Schema::dropIfExists('serieciasc.escola_lingua_indigena');
    }
}
