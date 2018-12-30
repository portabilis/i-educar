<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmiacoesAcaoGovernoFotoTable extends Migration
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
                
                CREATE TABLE pmiacoes.acao_governo_foto (
                    cod_acao_governo_foto integer DEFAULT nextval(\'pmiacoes.acao_governo_foto_cod_acao_governo_foto_seq\'::regclass) NOT NULL,
                    ref_funcionario_cad integer NOT NULL,
                    ref_cod_acao_governo integer NOT NULL,
                    nm_foto character varying(255) NOT NULL,
                    caminho character varying(255) NOT NULL,
                    data_foto timestamp without time zone,
                    data_cadastro timestamp without time zone NOT NULL
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
        Schema::dropIfExists('pmiacoes.acao_governo_foto');
    }
}