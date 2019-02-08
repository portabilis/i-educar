<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateSerieciascEscolaSanitarioTable extends Migration
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
                
                CREATE TABLE serieciasc.escola_sanitario (
                    ref_cod_escola integer NOT NULL,
                    rede_publica integer DEFAULT 0,
                    fossa integer DEFAULT 0,
                    inexistente integer DEFAULT 0,
                    created_at timestamp without time zone NOT NULL,
                    updated_at timestamp without time zone
                );
                
                ALTER TABLE ONLY serieciasc.escola_sanitario
                    ADD CONSTRAINT escola_sanitario_ref_cod_escola_pk PRIMARY KEY (ref_cod_escola);
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
        Schema::dropIfExists('serieciasc.escola_sanitario');
    }
}
