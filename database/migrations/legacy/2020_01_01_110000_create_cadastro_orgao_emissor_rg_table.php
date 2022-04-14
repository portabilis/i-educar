<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCadastroOrgaoEmissorRgTable extends Migration
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
                CREATE SEQUENCE cadastro.orgao_emissor_rg_idorg_rg_seq
                    START WITH 30
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE cadastro.orgao_emissor_rg (
                    idorg_rg integer DEFAULT nextval(\'cadastro.orgao_emissor_rg_idorg_rg_seq\'::regclass) NOT NULL,
                    sigla character varying(20) NOT NULL,
                    descricao character varying(60) NOT NULL,
                    situacao character(1) NOT NULL,
                    codigo_educacenso integer,
                    CONSTRAINT ck_orgao_emissor_rg_situacao CHECK (((situacao = \'A\'::bpchar) OR (situacao = \'I\'::bpchar)))
                );

                ALTER TABLE ONLY cadastro.orgao_emissor_rg
                    ADD CONSTRAINT pk_orgao_emissor_rg PRIMARY KEY (idorg_rg);

                SELECT pg_catalog.setval(\'cadastro.orgao_emissor_rg_idorg_rg_seq\', 31, true);
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
        Schema::dropIfExists('cadastro.orgao_emissor_rg');

        DB::unprepared('DROP SEQUENCE cadastro.orgao_emissor_rg_idorg_rg_seq;');
    }
}
