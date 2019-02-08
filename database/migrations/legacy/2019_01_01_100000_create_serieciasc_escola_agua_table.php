<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateSerieciascEscolaAguaTable extends Migration
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
                
                CREATE TABLE serieciasc.escola_agua (
                    ref_cod_escola integer NOT NULL,
                    rede_publica integer DEFAULT 0,
                    poco_artesiano integer DEFAULT 0,
                    cisterna integer DEFAULT 0,
                    fonte_rio integer DEFAULT 0,
                    inexistente integer DEFAULT 0,
                    created_at timestamp without time zone NOT NULL,
                    updated_at timestamp without time zone
                );
                
                ALTER TABLE ONLY serieciasc.escola_agua
                    ADD CONSTRAINT escola_agua_ref_cod_escola_pk PRIMARY KEY (ref_cod_escola);
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
        Schema::dropIfExists('serieciasc.escola_agua');
    }
}
