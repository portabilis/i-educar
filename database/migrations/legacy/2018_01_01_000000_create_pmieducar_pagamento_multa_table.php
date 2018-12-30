<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarPagamentoMultaTable extends Migration
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
                
                CREATE TABLE pmieducar.pagamento_multa (
                    cod_pagamento_multa integer DEFAULT nextval(\'pmieducar.pagamento_multa_cod_pagamento_multa_seq\'::regclass) NOT NULL,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_cliente integer NOT NULL,
                    valor_pago double precision NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    ref_cod_biblioteca integer NOT NULL
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
        Schema::dropIfExists('pmieducar.pagamento_multa');
    }
}
