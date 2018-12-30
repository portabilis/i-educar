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
                
                CREATE SEQUENCE alimentos.calendario_idcad_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE alimentos.calendario (
                    idcad integer DEFAULT nextval(\'alimentos.calendario_idcad_seq\'::regclass) NOT NULL,
                    idcli character varying(10) NOT NULL,
                    ano integer NOT NULL,
                    descricao character varying(40) NOT NULL
                );
                
                SELECT pg_catalog.setval(\'alimentos.calendario_idcad_seq\', 1, false);
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
