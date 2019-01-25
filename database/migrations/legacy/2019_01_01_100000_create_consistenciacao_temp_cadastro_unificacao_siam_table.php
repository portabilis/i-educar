<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateConsistenciacaoTempCadastroUnificacaoSiamTable extends Migration
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
                
                CREATE TABLE consistenciacao.temp_cadastro_unificacao_siam (
                    idpes numeric(8,0) NOT NULL,
                    nome character varying(40) NOT NULL,
                    cpf_cnpj character varying(14),
                    rg character varying(15),
                    logradouro character varying(40),
                    cep character varying(10),
                    bairro character varying(20),
                    cidade_end character varying(20),
                    uf_end character varying(2),
                    fone character varying(14),
                    data_cadastro character varying(10)
                );
                
                ALTER TABLE ONLY consistenciacao.temp_cadastro_unificacao_siam
                    ADD CONSTRAINT pk_temp_cadastro_unificacao_siam PRIMARY KEY (idpes);
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
        Schema::dropIfExists('consistenciacao.temp_cadastro_unificacao_siam');
    }
}
