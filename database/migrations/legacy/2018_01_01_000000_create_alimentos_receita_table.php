<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosReceitaTable extends Migration
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
                
                CREATE TABLE alimentos.receita (
                    idrec integer DEFAULT nextval(\'alimentos.receita_idrec_seq\'::regclass) NOT NULL,
                    idcli character varying(10) NOT NULL,
                    valor numeric NOT NULL,
                    descricao character varying(60) NOT NULL,
                    modo_preparo text NOT NULL,
                    rendimento integer NOT NULL,
                    valor_percapita numeric NOT NULL
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
        Schema::dropIfExists('alimentos.receita');
    }
}
