<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePublicBairroRegiaoTable extends Migration
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
                
                CREATE TABLE public.bairro_regiao (
                    ref_cod_regiao integer NOT NULL,
                    ref_idbai integer NOT NULL
                );
                
                ALTER TABLE ONLY public.bairro_regiao
                    ADD CONSTRAINT bairro_regiao_pkey PRIMARY KEY (ref_cod_regiao, ref_idbai);
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
        Schema::dropIfExists('public.bairro_regiao');
    }
}
