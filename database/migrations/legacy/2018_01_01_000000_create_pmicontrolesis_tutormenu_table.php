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
                
                CREATE TABLE pmicontrolesis.tutormenu (
                    cod_tutormenu integer DEFAULT nextval(\'pmicontrolesis.tutormenu_cod_tutormenu_seq\'::regclass) NOT NULL,
                    nm_tutormenu character varying(200) NOT NULL
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
        Schema::dropIfExists('pmicontrolesis.tutormenu');
    }
}
