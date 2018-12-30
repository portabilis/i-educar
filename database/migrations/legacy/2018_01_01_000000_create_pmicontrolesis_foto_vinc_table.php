<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmicontrolesisFotoVincTable extends Migration
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

                CREATE SEQUENCE pmicontrolesis.foto_vinc_cod_foto_vinc_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmicontrolesis.foto_vinc (
                    cod_foto_vinc integer DEFAULT nextval(\'pmicontrolesis.foto_vinc_cod_foto_vinc_seq\'::regclass) NOT NULL,
                    ref_cod_acontecimento integer NOT NULL,
                    ref_cod_foto_evento integer NOT NULL
                );
                
                SELECT pg_catalog.setval(\'pmicontrolesis.foto_vinc_cod_foto_vinc_seq\', 1, false);
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
        Schema::dropIfExists('pmicontrolesis.foto_vinc');
    }
}
