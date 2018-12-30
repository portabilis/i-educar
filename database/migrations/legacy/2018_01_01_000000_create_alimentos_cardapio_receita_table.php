<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosCardapioReceitaTable extends Migration
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
                
                CREATE TABLE alimentos.cardapio_receita (
                    idcar integer NOT NULL,
                    idrec integer NOT NULL
                );
                
                ALTER TABLE ONLY alimentos.cardapio_receita
                    ADD CONSTRAINT pk_cardapio_receita PRIMARY KEY (idcar, idrec);
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
        Schema::dropIfExists('alimentos.cardapio_receita');
    }
}
