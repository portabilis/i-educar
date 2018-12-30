<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosFaixaCompostoQuimicoTable extends Migration
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
                
                CREATE TABLE alimentos.faixa_composto_quimico (
                    idfcp integer DEFAULT nextval(\'alimentos.faixa_composto_quimico_idfcp_seq\'::regclass) NOT NULL,
                    idcom integer NOT NULL,
                    idfae integer NOT NULL,
                    quantidade numeric NOT NULL,
                    qtde_max_min character(3) NOT NULL,
                    CONSTRAINT ck_qtde_max_min CHECK (((qtde_max_min = \'MAX\'::bpchar) OR (qtde_max_min = \'MIN\'::bpchar)))
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
        Schema::dropIfExists('alimentos.faixa_composto_quimico');
    }
}
