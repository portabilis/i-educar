<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateCadastroHistoricoCartaoTable extends Migration
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
                
                CREATE TABLE cadastro.historico_cartao (
                    idpes_cidadao numeric(8,0) NOT NULL,
                    data_hora timestamp without time zone NOT NULL,
                    idpes_emitiu numeric(8,0) NOT NULL,
                    tipo character(1) NOT NULL,
                    CONSTRAINT ck_historico_cartao_tipo CHECK (((tipo = \'P\'::bpchar) OR (tipo = \'D\'::bpchar)))
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
        Schema::dropIfExists('cadastro.historico_cartao');
    }
}
