<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePublicPaisTable extends Migration
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
                
                CREATE TABLE public.pais (
                    idpais numeric(3,0) NOT NULL,
                    nome character varying(60) NOT NULL,
                    geom character varying,
                    cod_ibge integer
                );
                
                ALTER TABLE ONLY public.pais
                    ADD CONSTRAINT pk_pais PRIMARY KEY (idpais);
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
        Schema::dropIfExists('public.pais');
    }
}
