<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosUnidadeFaixaEtariaTable extends Migration
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
                
                CREATE TABLE alimentos.unidade_faixa_etaria (
                    idfeu integer DEFAULT nextval(\'alimentos.unidade_faixa_etaria_idfeu_seq\'::regclass) NOT NULL,
                    iduni integer NOT NULL,
                    idfae integer NOT NULL,
                    num_inscritos integer NOT NULL,
                    num_matriculados integer NOT NULL
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
        Schema::dropIfExists('alimentos.unidade_faixa_etaria');
    }
}
