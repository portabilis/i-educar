<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosProdutoTable extends Migration
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
                
                CREATE SEQUENCE alimentos.produto_idpro_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE alimentos.produto (
                    idpro integer DEFAULT nextval(\'alimentos.produto_idpro_seq\'::regclass) NOT NULL,
                    idcli character varying(10) NOT NULL,
                    idunp character varying(20) NOT NULL,
                    idfnde character varying(15) NOT NULL,
                    idtip integer NOT NULL,
                    descricao character varying(60) NOT NULL,
                    nome_compra character varying(60) NOT NULL,
                    referencia_ceasa character varying(1) NOT NULL,
                    fator_coccao numeric NOT NULL,
                    fator_correcao numeric NOT NULL,
                    vlr_unitario numeric NOT NULL,
                    penultimo_vlr_unitario numeric,
                    dt_ultima_compra timestamp without time zone,
                    dt_penultima_compra timestamp without time zone,
                    qtde_estoque numeric NOT NULL,
                    classe character varying(1) NOT NULL,
                    desc_composto character varying(300),
                    idfor integer,
                    CONSTRAINT ck_produto_classe CHECK ((((classe)::text = \'P\'::text) OR ((classe)::text = \'N\'::text))),
                    CONSTRAINT ck_produto_referencia_ceasa CHECK ((((referencia_ceasa)::text = \'1\'::text) OR ((referencia_ceasa)::text = \'0\'::text)))
                );
                
                SELECT pg_catalog.setval(\'alimentos.produto_idpro_seq\', 1, false);
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
        Schema::dropIfExists('alimentos.produto');
    }
}
