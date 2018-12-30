<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosFornecedorTable extends Migration
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
                
                CREATE SEQUENCE alimentos.fornecedor_idfor_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE alimentos.fornecedor (
                    idfor integer DEFAULT nextval(\'alimentos.fornecedor_idfor_seq\'::regclass) NOT NULL,
                    idpes integer NOT NULL,
                    idcli character varying(10) NOT NULL,
                    razao_social character varying(50) NOT NULL,
                    nome_fantasia character varying(50) NOT NULL,
                    endereco character varying(40) NOT NULL,
                    complemento character varying(30),
                    bairro character varying(30) NOT NULL,
                    cep character varying(8),
                    cidade character varying(18) NOT NULL,
                    uf character varying(2) NOT NULL,
                    telefone character varying(11) NOT NULL,
                    fax character varying(11),
                    email character varying(40),
                    contato character varying(40) NOT NULL,
                    cpf_cnpj character varying(14) NOT NULL,
                    inscr_estadual character varying(20),
                    inscr_municipal character varying(20),
                    tipo character(1) NOT NULL,
                    CONSTRAINT ck_fornecedor CHECK (((tipo = \'F\'::bpchar) OR (tipo = \'J\'::bpchar)))
                );
                
                SELECT pg_catalog.setval(\'alimentos.fornecedor_idfor_seq\', 1, false);
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
        Schema::dropIfExists('alimentos.fornecedor');
    }
}
