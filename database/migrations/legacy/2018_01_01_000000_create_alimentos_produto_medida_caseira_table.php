<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosProdutoMedidaCaseiraTable extends Migration
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
                
                CREATE TABLE alimentos.produto_medida_caseira (
                    idpmc integer DEFAULT nextval(\'alimentos.produto_medida_caseira_idpmc_seq\'::regclass) NOT NULL,
                    idcli character varying(10) NOT NULL,
                    idmedcas character varying(20) NOT NULL,
                    idpro integer NOT NULL,
                    peso numeric NOT NULL
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
        Schema::dropIfExists('alimentos.produto_medida_caseira');
    }
}
