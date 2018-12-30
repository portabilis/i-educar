<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosCalendarioTable extends Migration
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
                
                CREATE TABLE alimentos.calendario (
                    idcad integer DEFAULT nextval(\'alimentos.calendario_idcad_seq\'::regclass) NOT NULL,
                    idcli character varying(10) NOT NULL,
                    ano integer NOT NULL,
                    descricao character varying(40) NOT NULL
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
        Schema::dropIfExists('alimentos.calendario');
    }
}
