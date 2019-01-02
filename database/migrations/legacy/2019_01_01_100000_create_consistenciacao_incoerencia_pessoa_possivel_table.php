<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateConsistenciacaoIncoerenciaPessoaPossivelTable extends Migration
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
                
                CREATE TABLE consistenciacao.incoerencia_pessoa_possivel (
                    idinc integer NOT NULL,
                    idpes numeric(8,0) NOT NULL
                );
                
                ALTER TABLE ONLY consistenciacao.incoerencia_pessoa_possivel
                    ADD CONSTRAINT pk_inc_pessoa_possivel PRIMARY KEY (idinc, idpes);
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
        Schema::dropIfExists('consistenciacao.incoerencia_pessoa_possivel');
    }
}
