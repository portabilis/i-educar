<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosLogGuiaRemessaTable extends Migration
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
                
                CREATE SEQUENCE alimentos.log_guia_remessa_idlogguia_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE alimentos.log_guia_remessa (
                    idlogguia integer DEFAULT nextval(\'alimentos.log_guia_remessa_idlogguia_seq\'::regclass) NOT NULL,
                    login character varying(80) NOT NULL,
                    idcli character varying(10) NOT NULL,
                    dt_inicial date NOT NULL,
                    dt_final date NOT NULL,
                    unidade character varying(80) NOT NULL,
                    fornecedor character varying(80) NOT NULL,
                    classe character(2),
                    dt_geracao timestamp without time zone NOT NULL,
                    mensagem text NOT NULL
                );
                
                SELECT pg_catalog.setval(\'alimentos.log_guia_remessa_idlogguia_seq\', 1, false);
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
        Schema::dropIfExists('alimentos.log_guia_remessa');
    }
}
