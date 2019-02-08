<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarAcervoAcervoAssuntoTable extends Migration
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
                
                CREATE TABLE pmieducar.acervo_acervo_assunto (
                    ref_cod_acervo integer NOT NULL,
                    ref_cod_acervo_assunto integer NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.acervo_acervo_assunto
                    ADD CONSTRAINT acervo_acervo_assunto_pkey PRIMARY KEY (ref_cod_acervo, ref_cod_acervo_assunto);
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
        Schema::dropIfExists('pmieducar.acervo_acervo_assunto');
    }
}
