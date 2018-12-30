<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosReceitaProdutoTable extends Migration
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
                
                CREATE TABLE alimentos.receita_produto (
                    idrpr integer DEFAULT nextval(\'alimentos.receita_produto_idrpr_seq\'::regclass) NOT NULL,
                    idpro integer NOT NULL,
                    idrec integer NOT NULL,
                    idmedcas character varying(20),
                    quantidade numeric NOT NULL,
                    valor numeric NOT NULL,
                    qtdemedidacaseira integer NOT NULL,
                    valor_percapita numeric NOT NULL
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
        Schema::dropIfExists('alimentos.receita_produto');
    }
}
