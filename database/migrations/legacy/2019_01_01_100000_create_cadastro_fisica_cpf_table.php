<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateCadastroFisicaCpfTable extends Migration
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
                
                CREATE TABLE cadastro.fisica_cpf (
                    idpes numeric(8,0) NOT NULL,
                    cpf numeric(11,0) NOT NULL,
                    idpes_rev numeric,
                    data_rev timestamp without time zone,
                    origem_gravacao character(1) NOT NULL,
                    idpes_cad numeric,
                    data_cad timestamp without time zone NOT NULL,
                    operacao character(1) NOT NULL,
                    CONSTRAINT ck_fisica_cpf_operacao CHECK (((operacao = \'I\'::bpchar) OR (operacao = \'A\'::bpchar) OR (operacao = \'E\'::bpchar))),
                    CONSTRAINT ck_fisica_cpf_origem_gravacao CHECK (((origem_gravacao = \'M\'::bpchar) OR (origem_gravacao = \'U\'::bpchar) OR (origem_gravacao = \'C\'::bpchar) OR (origem_gravacao = \'O\'::bpchar)))
                );
                
                ALTER TABLE ONLY cadastro.fisica_cpf
                    ADD CONSTRAINT pk_fisica_cpf PRIMARY KEY (idpes);
                    
                CREATE UNIQUE INDEX un_fisica_cpf ON cadastro.fisica_cpf USING btree (cpf);
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
        Schema::dropIfExists('cadastro.fisica_cpf');
    }
}
