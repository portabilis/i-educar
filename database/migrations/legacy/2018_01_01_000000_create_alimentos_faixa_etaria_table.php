<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosFaixaEtariaTable extends Migration
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
                
                CREATE SEQUENCE alimentos.faixa_etaria_idfae_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE alimentos.faixa_etaria (
                    idfae integer DEFAULT nextval(\'alimentos.faixa_etaria_idfae_seq\'::regclass) NOT NULL,
                    idcli character varying(10) NOT NULL,
                    descricao character varying(50) NOT NULL,
                    vlr_base_refeicao numeric NOT NULL
                );
                
                SELECT pg_catalog.setval(\'alimentos.faixa_etaria_idfae_seq\', 1, false);
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
        Schema::dropIfExists('alimentos.faixa_etaria');
    }
}
