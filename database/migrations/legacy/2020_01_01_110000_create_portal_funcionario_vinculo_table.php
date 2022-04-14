<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePortalFuncionarioVinculoTable extends Migration
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
                CREATE SEQUENCE portal.funcionario_vinculo_cod_funcionario_vinculo_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE portal.funcionario_vinculo (
                    cod_funcionario_vinculo integer DEFAULT nextval(\'portal.funcionario_vinculo_cod_funcionario_vinculo_seq\'::regclass) NOT NULL,
                    nm_vinculo character varying(255) DEFAULT \'\'::character varying NOT NULL,
                    abreviatura character varying(16)
                );

                ALTER TABLE ONLY portal.funcionario_vinculo
                    ADD CONSTRAINT funcionario_vinculo_pk PRIMARY KEY (cod_funcionario_vinculo);

                SELECT pg_catalog.setval(\'portal.funcionario_vinculo_cod_funcionario_vinculo_seq\', 7, true);
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
        Schema::dropIfExists('portal.funcionario_vinculo');

        DB::unprepared('DROP SEQUENCE portal.funcionario_vinculo_cod_funcionario_vinculo_seq;');
    }
}
