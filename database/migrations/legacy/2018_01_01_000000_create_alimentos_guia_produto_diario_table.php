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
                
                CREATE SEQUENCE alimentos.guia_produto_diario_idguiaprodiario_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE alimentos.guia_produto_diario (
                    idguiaprodiario integer DEFAULT nextval(\'alimentos.guia_produto_diario_idguiaprodiario_seq\'::regclass) NOT NULL,
                    idgui integer NOT NULL,
                    idpro integer NOT NULL,
                    iduni integer NOT NULL,
                    dt_guia date NOT NULL,
                    qtde numeric NOT NULL
                );
                
                SELECT pg_catalog.setval(\'alimentos.guia_produto_diario_idguiaprodiario_seq\', 1, false);
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
