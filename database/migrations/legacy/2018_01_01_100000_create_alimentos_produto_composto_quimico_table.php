<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosProdutoCompostoQuimicoTable extends Migration
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
                
                CREATE SEQUENCE alimentos.produto_composto_quimico_idpcq_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE alimentos.produto_composto_quimico (
                    idpcq integer DEFAULT nextval(\'alimentos.produto_composto_quimico_idpcq_seq\'::regclass) NOT NULL,
                    idpro integer NOT NULL,
                    idcom integer NOT NULL,
                    quantidade numeric NOT NULL
                );
                
                ALTER TABLE ONLY alimentos.produto_composto_quimico
                    ADD CONSTRAINT pk_prod_cp_quimico PRIMARY KEY (idpcq);

                SELECT pg_catalog.setval(\'alimentos.produto_composto_quimico_idpcq_seq\', 1, false);
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
        Schema::dropIfExists('alimentos.produto_composto_quimico');

        DB::unprepared('DROP SEQUENCE alimentos.produto_composto_quimico_idpcq_seq;');
    }
}
