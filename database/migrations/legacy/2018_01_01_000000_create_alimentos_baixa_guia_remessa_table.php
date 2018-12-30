<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosBaixaGuiaRemessaTable extends Migration
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
                
                CREATE TABLE alimentos.baixa_guia_remessa (
                    idbai integer DEFAULT nextval(\'alimentos.baixa_guia_remessa_idbai_seq\'::regclass) NOT NULL,
                    login_baixa character varying(80) NOT NULL,
                    idgui integer NOT NULL,
                    dt_recebimento date NOT NULL,
                    nome_recebedor character varying(40) NOT NULL,
                    cargo_recebedor character varying(40) NOT NULL,
                    dt_operacao date NOT NULL
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
        Schema::dropIfExists('alimentos.baixa_guia_remessa');
    }
}