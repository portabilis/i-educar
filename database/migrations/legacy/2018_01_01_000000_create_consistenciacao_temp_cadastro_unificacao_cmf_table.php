<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateConsistenciacaoTempCadastroUnificacaoCmfTable extends Migration
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
                
                CREATE TABLE consistenciacao.temp_cadastro_unificacao_cmf (
                    idpes numeric(8,0) NOT NULL,
                    nome character varying(150) NOT NULL,
                    cpf_cnpj character varying(14),
                    rg character varying(10),
                    uf_rg character varying(50),
                    data_nascimento character varying(10),
                    logradouro character varying(150),
                    cep character varying(10),
                    bairro character varying(40),
                    numero character varying(6),
                    cidade_end character varying(60),
                    uf_end character varying(2),
                    complemento character varying(20),
                    fone character varying(14),
                    nome_mae character varying(150),
                    nome_mae_idpes character varying(150),
                    data_cadastro character varying(10),
                    data_atualizacao character varying(10),
                    situacao character varying(15),
                    tipo_pess character varying(1),
                    nome_fantasia character varying(50),
                    inscr_estadual character varying(10)
                );
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
        Schema::dropIfExists('consistenciacao.temp_cadastro_unificacao_cmf');
    }
}
