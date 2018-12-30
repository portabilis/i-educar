<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateHistoricoFisicaTable extends Migration
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
                
                CREATE TABLE historico.fisica (
                    idpes numeric(8,0) NOT NULL,
                    data_nasc date,
                    sexo character(1),
                    idpes_mae numeric(8,0),
                    idpes_pai numeric(8,0),
                    idpes_responsavel numeric(8,0),
                    idesco numeric(2,0),
                    ideciv numeric(1,0),
                    idpes_con numeric(8,0),
                    data_uniao date,
                    data_obito date,
                    nacionalidade numeric(1,0),
                    idpais_estrangeiro numeric(3,0),
                    data_chegada_brasil date,
                    idmun_nascimento numeric(6,0),
                    ultima_empresa character varying(150),
                    idocup numeric(6,0),
                    nome_mae character varying(150),
                    nome_pai character varying(150),
                    nome_conjuge character varying(150),
                    nome_responsavel character varying(150),
                    justificativa_provisorio character varying(150),
                    idpes_rev numeric,
                    idsis_rev numeric,
                    data_rev timestamp without time zone,
                    origem_gravacao character(1) NOT NULL,
                    idpes_cad numeric,
                    idsis_cad numeric NOT NULL,
                    data_cad timestamp without time zone NOT NULL,
                    operacao character(1) NOT NULL,
                    CONSTRAINT ck_fisica_nacionalidade CHECK (((nacionalidade >= (1)::numeric) AND (nacionalidade <= (3)::numeric))),
                    CONSTRAINT ck_fisica_operacao CHECK (((operacao = \'I\'::bpchar) OR (operacao = \'A\'::bpchar) OR (operacao = \'E\'::bpchar))),
                    CONSTRAINT ck_fisica_origem_gravacao CHECK (((origem_gravacao = \'M\'::bpchar) OR (origem_gravacao = \'U\'::bpchar) OR (origem_gravacao = \'C\'::bpchar) OR (origem_gravacao = \'O\'::bpchar))),
                    CONSTRAINT ck_fisica_sexo CHECK (((sexo = \'M\'::bpchar) OR (sexo = \'F\'::bpchar)))
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
        Schema::dropIfExists('historico.fisica');
    }
}
