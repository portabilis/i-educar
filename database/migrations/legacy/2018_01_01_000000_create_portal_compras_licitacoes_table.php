<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalComprasLicitacoesTable extends Migration
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

                CREATE SEQUENCE portal.compras_licitacoes_cod_compras_licitacoes_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE portal.compras_licitacoes (
                    cod_compras_licitacoes integer DEFAULT nextval(\'portal.compras_licitacoes_cod_compras_licitacoes_seq\'::regclass) NOT NULL,
                    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
                    ref_cod_compras_modalidade integer DEFAULT 0 NOT NULL,
                    numero character varying(30) DEFAULT \'\'::character varying NOT NULL,
                    objeto text NOT NULL,
                    data_hora timestamp without time zone NOT NULL,
                    cod_licitacao_semasa integer,
                    oculto boolean DEFAULT false
                );
                
                SELECT pg_catalog.setval(\'portal.compras_licitacoes_cod_compras_licitacoes_seq\', 1, false);
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
        Schema::dropIfExists('portal.compras_licitacoes');
    }
}
