<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosTipoProdutoTable extends Migration
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
                
                CREATE SEQUENCE alimentos.tipo_produto_idtip_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE alimentos.tipo_produto (
                    idtip integer DEFAULT nextval(\'alimentos.tipo_produto_idtip_seq\'::regclass) NOT NULL,
                    idcli character varying(10) NOT NULL,
                    descricao character varying(50) NOT NULL
                );
                
                SELECT pg_catalog.setval(\'alimentos.tipo_produto_idtip_seq\', 1, false);
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
        Schema::dropIfExists('alimentos.tipo_produto');
    }
}
