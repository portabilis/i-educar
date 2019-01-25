<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateHistoricoEnderecoPessoaTable extends Migration
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
                
                CREATE TABLE historico.endereco_pessoa (
                    idpes numeric(8,0) NOT NULL,
                    tipo numeric(1,0) NOT NULL,
                    cep numeric(8,0) NOT NULL,
                    idlog numeric(6,0) NOT NULL,
                    idbai numeric(6,0) NOT NULL,
                    numero numeric(6,0),
                    letra character(1),
                    complemento character varying(50),
                    reside_desde date,
                    idpes_rev numeric,
                    idsis_rev numeric,
                    data_rev timestamp without time zone,
                    origem_gravacao character(1) NOT NULL,
                    idpes_cad numeric,
                    idsis_cad numeric NOT NULL,
                    data_cad timestamp without time zone NOT NULL,
                    operacao character(1) NOT NULL,
                    CONSTRAINT ck_endereco_pessoa_operacao CHECK (((operacao = \'I\'::bpchar) OR (operacao = \'A\'::bpchar) OR (operacao = \'E\'::bpchar))),
                    CONSTRAINT ck_endereco_pessoa_origem_gravacao CHECK (((origem_gravacao = \'M\'::bpchar) OR (origem_gravacao = \'U\'::bpchar) OR (origem_gravacao = \'C\'::bpchar) OR (origem_gravacao = \'O\'::bpchar))),
                    CONSTRAINT ck_endereco_pessoa_tipo CHECK (((tipo >= (1)::numeric) AND (tipo <= (3)::numeric)))
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
        Schema::dropIfExists('historico.endereco_pessoa');
    }
}
