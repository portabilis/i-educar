<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosProdutoFornecedorTable extends Migration
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
                
                CREATE TABLE alimentos.produto_fornecedor (
                    idprf integer DEFAULT nextval(\'alimentos.produto_fornecedor_idprf_seq\'::regclass) NOT NULL,
                    idfor integer NOT NULL,
                    idpro integer NOT NULL,
                    codigo_ean character varying(18) NOT NULL
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
        Schema::dropIfExists('alimentos.produto_fornecedor');
    }
}
