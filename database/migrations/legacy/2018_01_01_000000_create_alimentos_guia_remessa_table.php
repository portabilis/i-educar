<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosGuiaRemessaTable extends Migration
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
                
                CREATE TABLE alimentos.guia_remessa (
                    idgui integer DEFAULT nextval(\'alimentos.guia_remessa_idgui_seq\'::regclass) NOT NULL,
                    idcon integer NOT NULL,
                    login_cancelamento character varying(80),
                    login_emissao character varying(80) NOT NULL,
                    idfor integer NOT NULL,
                    iduni integer NOT NULL,
                    idcli character varying(10) NOT NULL,
                    dt_emissao timestamp without time zone NOT NULL,
                    ano integer NOT NULL,
                    sequencial integer NOT NULL,
                    dt_cardapio_inicial date NOT NULL,
                    dt_cardapio_final date NOT NULL,
                    num_inscr_matr integer NOT NULL,
                    num_refeicao integer NOT NULL,
                    situacao character(1) NOT NULL,
                    dt_cancelamento timestamp without time zone,
                    justificativa_cancelamento character varying(300),
                    classe_produto character varying(2) NOT NULL,
                    CONSTRAINT ck_guia_remessa_classe_produto CHECK ((((classe_produto)::text = \'P\'::text) OR ((classe_produto)::text = \'N\'::text) OR ((classe_produto)::text = \'PN\'::text))),
                    CONSTRAINT ck_guia_remessa_situacao CHECK (((situacao = \'E\'::bpchar) OR (situacao = \'R\'::bpchar) OR (situacao = \'C\'::bpchar) OR (situacao = \'P\'::bpchar)))
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
        Schema::dropIfExists('alimentos.guia_remessa');
    }
}
