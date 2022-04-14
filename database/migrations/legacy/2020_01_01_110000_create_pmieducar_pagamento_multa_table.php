<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
                CREATE SEQUENCE pmieducar.pagamento_multa_cod_pagamento_multa_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.pagamento_multa (
                    cod_pagamento_multa integer DEFAULT nextval(\'pmieducar.pagamento_multa_cod_pagamento_multa_seq\'::regclass) NOT NULL,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_cliente integer NOT NULL,
                    valor_pago double precision NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    ref_cod_biblioteca integer NOT NULL
                );

                ALTER TABLE ONLY pmieducar.pagamento_multa
                    ADD CONSTRAINT pagamento_multa_pkey PRIMARY KEY (cod_pagamento_multa);

                SELECT pg_catalog.setval(\'pmieducar.pagamento_multa_cod_pagamento_multa_seq\', 1, false);
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

        DB::unprepared('DROP SEQUENCE pmieducar.pagamento_multa_cod_pagamento_multa_seq;');
    }
}
