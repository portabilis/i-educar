<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarTipoAvaliacaoValoresTable extends Migration
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
                
                CREATE TABLE pmieducar.tipo_avaliacao_valores (
                    ref_cod_tipo_avaliacao integer NOT NULL,
                    sequencial integer NOT NULL,
                    nome character varying(255) NOT NULL,
                    valor double precision NOT NULL,
                    valor_min double precision NOT NULL,
                    valor_max double precision NOT NULL,
                    ativo boolean DEFAULT true
                );
                
                ALTER TABLE ONLY pmieducar.tipo_avaliacao_valores
                    ADD CONSTRAINT tipo_avaliacao_valores_pkey PRIMARY KEY (ref_cod_tipo_avaliacao, sequencial);
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
        Schema::dropIfExists('pmieducar.tipo_avaliacao_valores');
    }
}
