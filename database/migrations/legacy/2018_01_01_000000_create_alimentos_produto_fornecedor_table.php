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
                
                CREATE SEQUENCE alimentos.produto_fornecedor_idprf_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE alimentos.produto_fornecedor (
                    idprf integer DEFAULT nextval(\'alimentos.produto_fornecedor_idprf_seq\'::regclass) NOT NULL,
                    idfor integer NOT NULL,
                    idpro integer NOT NULL,
                    codigo_ean character varying(18) NOT NULL
                );
                
                SELECT pg_catalog.setval(\'alimentos.produto_fornecedor_idprf_seq\', 1, false);
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
