<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCadastroJuridicaTable extends Migration
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
                CREATE TABLE cadastro.juridica (
                    idpes numeric(8,0) NOT NULL,
                    cnpj numeric(14,0) NOT NULL,
                    insc_estadual numeric(20,0),
                    idpes_rev numeric,
                    data_rev timestamp without time zone,
                    origem_gravacao character(1) NOT NULL,
                    idpes_cad numeric,
                    data_cad timestamp without time zone NOT NULL,
                    operacao character(1) NOT NULL,
                    fantasia character varying(255),
                    capital_social character varying(255),
                    CONSTRAINT ck_juridica_operacao CHECK (((operacao = \'I\'::bpchar) OR (operacao = \'A\'::bpchar) OR (operacao = \'E\'::bpchar))),
                    CONSTRAINT ck_juridica_origem_gravacao CHECK (((origem_gravacao = \'M\'::bpchar) OR (origem_gravacao = \'U\'::bpchar) OR (origem_gravacao = \'C\'::bpchar) OR (origem_gravacao = \'O\'::bpchar)))
                );

                ALTER TABLE ONLY cadastro.juridica
                    ADD CONSTRAINT pk_juridica PRIMARY KEY (idpes);

                CREATE UNIQUE INDEX un_juridica_cnpj ON cadastro.juridica USING btree (cnpj);
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
        Schema::dropIfExists('cadastro.juridica');
    }
}
