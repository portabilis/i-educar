<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosCardapioProdutoTable extends Migration
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
                
                CREATE TABLE alimentos.cardapio_produto (
                    idcpr integer DEFAULT nextval(\'alimentos.cardapio_produto_idcpr_seq\'::regclass) NOT NULL,
                    idpro integer NOT NULL,
                    idcar integer NOT NULL,
                    quantidade numeric NOT NULL,
                    valor numeric NOT NULL
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
        Schema::dropIfExists('alimentos.cardapio_produto');
    }
}
