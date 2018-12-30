<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalComprasPregaoExecucaoTable extends Migration
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

                CREATE SEQUENCE portal.compras_pregao_execucao_cod_compras_pregao_execucao_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE portal.compras_pregao_execucao (
                    cod_compras_pregao_execucao integer DEFAULT nextval(\'portal.compras_pregao_execucao_cod_compras_pregao_execucao_seq\'::regclass) NOT NULL,
                    ref_cod_compras_licitacoes integer DEFAULT 0 NOT NULL,
                    ref_pregoeiro integer DEFAULT 0 NOT NULL,
                    ref_equipe1 integer DEFAULT 0 NOT NULL,
                    ref_equipe2 integer DEFAULT 0 NOT NULL,
                    ref_equipe3 integer DEFAULT 0 NOT NULL,
                    ano_processo integer,
                    mes_processo integer,
                    seq_processo integer,
                    seq_portaria integer,
                    ano_portaria integer,
                    valor_referencia double precision,
                    valor_real double precision,
                    ref_cod_compras_final_pregao integer
                );
                
                SELECT pg_catalog.setval(\'portal.compras_pregao_execucao_cod_compras_pregao_execucao_seq\', 1, false);
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
        Schema::dropIfExists('portal.compras_pregao_execucao');
    }
}
