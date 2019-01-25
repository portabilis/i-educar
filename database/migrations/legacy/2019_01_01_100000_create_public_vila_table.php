<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePublicVilaTable extends Migration
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
                
                CREATE TABLE public.vila (
                    idvil numeric(4,0) NOT NULL,
                    idmun numeric(6,0) NOT NULL,
                    nome character varying(50) NOT NULL,
                    geom character varying
                );
                
                ALTER TABLE ONLY public.vila
                    ADD CONSTRAINT pk_vila PRIMARY KEY (idvil);
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
        Schema::dropIfExists('public.vila');
    }
}
