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
                
                CREATE SEQUENCE alimentos.receita_produto_idrpr_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

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
                
                ALTER TABLE ONLY alimentos.receita_produto
                    ADD CONSTRAINT pk_rec_prod PRIMARY KEY (idrpr);

                CREATE UNIQUE INDEX un_rec_prod ON alimentos.receita_produto USING btree (idpro, idrec);

                SELECT pg_catalog.setval(\'alimentos.receita_produto_idrpr_seq\', 1, false);
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

        DB::unprepared('DROP SEQUENCE alimentos.receita_produto_idrpr_seq;');
    }
}
