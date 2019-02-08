<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosFaixaCompostoQuimicoTable extends Migration
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
                
                CREATE SEQUENCE alimentos.faixa_composto_quimico_idfcp_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE alimentos.faixa_composto_quimico (
                    idfcp integer DEFAULT nextval(\'alimentos.faixa_composto_quimico_idfcp_seq\'::regclass) NOT NULL,
                    idcom integer NOT NULL,
                    idfae integer NOT NULL,
                    quantidade numeric NOT NULL,
                    qtde_max_min character(3) NOT NULL,
                    CONSTRAINT ck_qtde_max_min CHECK (((qtde_max_min = \'MAX\'::bpchar) OR (qtde_max_min = \'MIN\'::bpchar)))
                );
                
                ALTER TABLE ONLY alimentos.faixa_composto_quimico
                    ADD CONSTRAINT pk_faixa_composto_quimico PRIMARY KEY (idfcp);

                CREATE UNIQUE INDEX un_faixa_cp_quimico ON alimentos.faixa_composto_quimico USING btree (idcom, idfae);

                SELECT pg_catalog.setval(\'alimentos.faixa_composto_quimico_idfcp_seq\', 1, false);
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
        Schema::dropIfExists('alimentos.faixa_composto_quimico');

        DB::unprepared('DROP SEQUENCE alimentos.faixa_composto_quimico_idfcp_seq;');
    }
}
