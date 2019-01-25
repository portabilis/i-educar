<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosBaixaGuiaProdutoTable extends Migration
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
                
                CREATE SEQUENCE alimentos.baixa_guia_produto_idbap_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE alimentos.baixa_guia_produto (
                    idbap integer DEFAULT nextval(\'alimentos.baixa_guia_produto_idbap_seq\'::regclass) NOT NULL,
                    idgup integer NOT NULL,
                    idbai integer NOT NULL,
                    dt_validade date,
                    qtde_recebida numeric NOT NULL,
                    dt_operacao date NOT NULL,
                    login_baixa character varying(80) NOT NULL
                );
                
                ALTER TABLE ONLY alimentos.baixa_guia_produto
                    ADD CONSTRAINT pk_baixa_guia_produto PRIMARY KEY (idbap);

                SELECT pg_catalog.setval(\'alimentos.baixa_guia_produto_idbap_seq\', 1, false);
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
        Schema::dropIfExists('alimentos.baixa_guia_produto');

        DB::unprepared('DROP SEQUENCE alimentos.baixa_guia_produto_idbap_seq;');
    }
}
