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
                
                CREATE SEQUENCE alimentos.cardapio_produto_idcpr_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE alimentos.cardapio_produto (
                    idcpr integer DEFAULT nextval(\'alimentos.cardapio_produto_idcpr_seq\'::regclass) NOT NULL,
                    idpro integer NOT NULL,
                    idcar integer NOT NULL,
                    quantidade numeric NOT NULL,
                    valor numeric NOT NULL
                );
                
                ALTER TABLE ONLY alimentos.cardapio_produto
                    ADD CONSTRAINT pk_cardapio_produto PRIMARY KEY (idcpr);

                CREATE UNIQUE INDEX un_cardapio_produto ON alimentos.cardapio_produto USING btree (idcar, idpro);

                SELECT pg_catalog.setval(\'alimentos.cardapio_produto_idcpr_seq\', 1, false);
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

        DB::unprepared('DROP SEQUENCE alimentos.cardapio_produto_idcpr_seq;');
    }
}
