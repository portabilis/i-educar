<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmicontrolesisTutorMenuTable extends Migration
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
                
                CREATE SEQUENCE pmicontrolesis.tutormenu_cod_tutormenu_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmicontrolesis.tutormenu (
                    cod_tutormenu integer DEFAULT nextval(\'pmicontrolesis.tutormenu_cod_tutormenu_seq\'::regclass) NOT NULL,
                    nm_tutormenu character varying(200) NOT NULL
                );
                
                SELECT pg_catalog.setval(\'pmicontrolesis.tutormenu_cod_tutormenu_seq\', 16, true);
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
        Schema::dropIfExists('pmicontrolesis.tutormenu');
    }
}
