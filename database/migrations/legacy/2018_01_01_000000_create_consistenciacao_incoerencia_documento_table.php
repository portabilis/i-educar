<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateConsistenciacaoIncoerenciaDocumentoTable extends Migration
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
                
                CREATE SEQUENCE consistenciacao.incoerencia_documento_id_inc_doc_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE consistenciacao.incoerencia_documento (
                    id_inc_doc integer DEFAULT nextval(\'consistenciacao.incoerencia_documento_id_inc_doc_seq\'::regclass) NOT NULL,
                    idinc integer NOT NULL,
                    rg numeric(10,0),
                    orgao_exp_rg character varying(20),
                    data_exp_rg character varying(20),
                    sigla_uf_rg_exp character varying(30),
                    tipo_cert_civil numeric(2,0),
                    num_termo numeric(8,0),
                    num_livro numeric(8,0),
                    num_folha numeric(4,0),
                    data_emissao_cert_civil character varying(20),
                    cartorio_cert_civil character varying(150),
                    sigla_uf_cert_civil character varying(30),
                    num_cart_trabalho numeric(7,0),
                    serie_cart_trabalho numeric(5,0),
                    data_emissao_cart_trabalho character varying(20),
                    sigla_uf_cart_trabalho character varying(30),
                    num_tit_eleitor numeric(13,0),
                    zona_tit_eleitor numeric(4,0),
                    secao_tit_eleitor numeric(4,0)
                );
                
                SELECT pg_catalog.setval(\'consistenciacao.incoerencia_documento_id_inc_doc_seq\', 1, false);
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
        Schema::dropIfExists('consistenciacao.incoerencia_documento');
    }
}
