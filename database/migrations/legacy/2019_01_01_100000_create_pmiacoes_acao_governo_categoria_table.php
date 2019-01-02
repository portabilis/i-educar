<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmiacoesAcaoGovernoCategoriaTable extends Migration
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
                
                CREATE TABLE pmiacoes.acao_governo_categoria (
                    ref_cod_categoria integer NOT NULL,
                    ref_cod_acao_governo integer NOT NULL
                );
                
                ALTER TABLE ONLY pmiacoes.acao_governo_categoria
                    ADD CONSTRAINT acao_governo_categoria_pkey PRIMARY KEY (ref_cod_categoria, ref_cod_acao_governo);
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
        Schema::dropIfExists('pmiacoes.acao_governo_categoria');
    }
}
