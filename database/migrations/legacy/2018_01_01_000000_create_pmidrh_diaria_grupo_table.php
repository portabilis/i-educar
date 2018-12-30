<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmidrhDiariaGrupoTable extends Migration
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
                SET default_with_oids = true;
                
                CREATE TABLE pmidrh.diaria_grupo (
                    cod_diaria_grupo integer DEFAULT nextval(\'pmidrh.diaria_grupo_cod_diaria_grupo_seq\'::regclass) NOT NULL,
                    desc_grupo character varying(255) NOT NULL
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
        Schema::dropIfExists('pmidrh.diaria_grupo');
    }
}
