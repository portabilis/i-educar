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
                
                CREATE SEQUENCE alimentos.produto_medida_caseira_idpmc_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE alimentos.produto_medida_caseira (
                    idpmc integer DEFAULT nextval(\'alimentos.produto_medida_caseira_idpmc_seq\'::regclass) NOT NULL,
                    idcli character varying(10) NOT NULL,
                    idmedcas character varying(20) NOT NULL,
                    idpro integer NOT NULL,
                    peso numeric NOT NULL
                );
                
                SELECT pg_catalog.setval(\'alimentos.produto_medida_caseira_idpmc_seq\', 1, false);
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
