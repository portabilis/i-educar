<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePublicUfTable extends Migration
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
                
                CREATE TABLE public.uf (
                    sigla_uf character varying(3) NOT NULL,
                    nome character varying(30) NOT NULL,
                    geom character varying,
                    idpais numeric(3,0),
                    cod_ibge numeric(6,0)
                );
                
                ALTER TABLE ONLY public.uf
                    ADD CONSTRAINT pk_uf PRIMARY KEY (sigla_uf);
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
        Schema::dropIfExists('public.uf');
    }
}
