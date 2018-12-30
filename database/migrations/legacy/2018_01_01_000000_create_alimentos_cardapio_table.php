<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosCardapioTable extends Migration
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
                
                CREATE SEQUENCE alimentos.cardapio_idcar_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE alimentos.cardapio (
                    idcar integer DEFAULT nextval(\'alimentos.cardapio_idcar_seq\'::regclass) NOT NULL,
                    login_inclusao character varying(80) NOT NULL,
                    login_alteracao character varying(80),
                    idcli character varying(10) NOT NULL,
                    idtre integer NOT NULL,
                    dt_cardapio date NOT NULL,
                    dt_inclusao timestamp without time zone NOT NULL,
                    dt_ultima_alteracao timestamp without time zone,
                    valor numeric NOT NULL,
                    finalizado character(1) NOT NULL,
                    CONSTRAINT ck_cardapio_finalizado CHECK (((finalizado = \'S\'::bpchar) OR (finalizado = \'N\'::bpchar)))
                );
                
                SELECT pg_catalog.setval(\'alimentos.cardapio_idcar_seq\', 1, false);
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
        Schema::dropIfExists('alimentos.cardapio');
    }
}
