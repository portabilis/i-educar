<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosGuiaRemessaProdutoTable extends Migration
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
                
                CREATE SEQUENCE alimentos.guia_remessa_produto_idgup_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE alimentos.guia_remessa_produto (
                    idgup integer DEFAULT nextval(\'alimentos.guia_remessa_produto_idgup_seq\'::regclass) NOT NULL,
                    idgui integer NOT NULL,
                    idpro integer NOT NULL,
                    qtde_per_capita numeric NOT NULL,
                    qtde_guia numeric NOT NULL,
                    peso numeric NOT NULL,
                    qtde_recebida numeric NOT NULL,
                    peso_total numeric NOT NULL
                );
                
                SELECT pg_catalog.setval(\'alimentos.guia_remessa_produto_idgup_seq\', 1, false);
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
        Schema::dropIfExists('alimentos.guia_remessa_produto');
    }
}
