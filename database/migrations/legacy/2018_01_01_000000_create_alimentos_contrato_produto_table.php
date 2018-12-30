<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosContratoProdutoTable extends Migration
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
                
                CREATE TABLE alimentos.contrato_produto (
                    idcop integer DEFAULT nextval(\'alimentos.contrato_produto_idcop_seq\'::regclass) NOT NULL,
                    idcon integer NOT NULL,
                    idpro integer NOT NULL,
                    qtde_contratada numeric NOT NULL,
                    vlr_unitario_atual numeric NOT NULL,
                    qtde_remessa numeric NOT NULL,
                    qtde_recebida numeric NOT NULL,
                    qtde_aditivo numeric NOT NULL,
                    vlr_unitario_original numeric NOT NULL,
                    operacao character(1) NOT NULL,
                    ajuste numeric,
                    CONSTRAINT ck_contrato_operacao CHECK (((operacao = \'I\'::bpchar) OR (operacao = \'A\'::bpchar) OR (operacao = \'E\'::bpchar) OR (operacao = \'N\'::bpchar)))
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
        Schema::dropIfExists('alimentos.contrato_produto');
    }
}
