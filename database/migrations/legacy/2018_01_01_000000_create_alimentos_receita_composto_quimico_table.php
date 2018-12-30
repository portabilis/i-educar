<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosReceitaCompostoQuimicoTable extends Migration
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
                
                CREATE SEQUENCE alimentos.receita_composto_quimico_idrcq_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE alimentos.receita_composto_quimico (
                    idrcq integer DEFAULT nextval(\'alimentos.receita_composto_quimico_idrcq_seq\'::regclass) NOT NULL,
                    idcom integer NOT NULL,
                    idrec integer NOT NULL,
                    quantidade numeric NOT NULL
                );
                
                ALTER TABLE ONLY alimentos.receita_composto_quimico
                    ADD CONSTRAINT pk_rec_cp_quimico PRIMARY KEY (idrcq);

                SELECT pg_catalog.setval(\'alimentos.receita_composto_quimico_idrcq_seq\', 1, false);
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
        Schema::dropIfExists('alimentos.receita_composto_quimico');

        DB::unprepared('DROP SEQUENCE alimentos.receita_composto_quimico_idrcq_seq;');
    }
}
