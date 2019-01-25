<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosTipoRefeicaoTable extends Migration
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
                
                CREATE SEQUENCE alimentos.tipo_refeicao_idtre_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE alimentos.tipo_refeicao (
                    idtre integer DEFAULT nextval(\'alimentos.tipo_refeicao_idtre_seq\'::regclass) NOT NULL,
                    idcli character varying(10) NOT NULL,
                    descricao character varying(30) NOT NULL
                );
                
                ALTER TABLE ONLY alimentos.tipo_refeicao
                    ADD CONSTRAINT pk_tp_refeicao PRIMARY KEY (idtre);

                SELECT pg_catalog.setval(\'alimentos.tipo_refeicao_idtre_seq\', 1, false);
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
        Schema::dropIfExists('alimentos.tipo_refeicao');

        DB::unprepared('DROP SEQUENCE alimentos.tipo_refeicao_idtre_seq;');
    }
}
