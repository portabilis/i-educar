<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosGuiaProdutoDiarioTable extends Migration
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
                
                CREATE TABLE alimentos.guia_produto_diario (
                    idguiaprodiario integer DEFAULT nextval(\'alimentos.guia_produto_diario_idguiaprodiario_seq\'::regclass) NOT NULL,
                    idgui integer NOT NULL,
                    idpro integer NOT NULL,
                    iduni integer NOT NULL,
                    dt_guia date NOT NULL,
                    qtde numeric NOT NULL
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
        Schema::dropIfExists('alimentos.guia_produto_diario');
    }
}
