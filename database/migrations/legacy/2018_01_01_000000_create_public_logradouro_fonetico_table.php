<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePublicLogradouroFoneticoTable extends Migration
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
                
                CREATE TABLE public.logradouro_fonetico (
                    fonema character varying(30) NOT NULL,
                    idlog numeric(8,0) NOT NULL
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
        Schema::dropIfExists('public.logradouro_fonetico');
    }
}
