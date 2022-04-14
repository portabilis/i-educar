<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCadastroFonePessoaTable extends Migration
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
                CREATE TABLE cadastro.fone_pessoa (
                    idpes numeric(8,0) NOT NULL,
                    tipo numeric(1,0) NOT NULL,
                    ddd numeric(3,0) NOT NULL,
                    fone numeric(11,0) NOT NULL,
                    idpes_rev numeric,
                    data_rev timestamp without time zone,
                    origem_gravacao character(1) NOT NULL,
                    idpes_cad numeric,
                    data_cad timestamp without time zone NOT NULL,
                    operacao character(1) NOT NULL,
                    CONSTRAINT ck_fone_pessoa_operacao CHECK (((operacao = \'I\'::bpchar) OR (operacao = \'A\'::bpchar) OR (operacao = \'E\'::bpchar))),
                    CONSTRAINT ck_fone_pessoa_origem_gravacao CHECK (((origem_gravacao = \'M\'::bpchar) OR (origem_gravacao = \'U\'::bpchar) OR (origem_gravacao = \'C\'::bpchar) OR (origem_gravacao = \'O\'::bpchar))),
                    CONSTRAINT ck_fone_pessoa_tipo CHECK (((tipo >= (1)::numeric) AND (tipo <= (4)::numeric)))
                );

                ALTER TABLE ONLY cadastro.fone_pessoa
                    ADD CONSTRAINT pk_fone_pessoa PRIMARY KEY (idpes, tipo);
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
        Schema::dropIfExists('cadastro.fone_pessoa');
    }
}
