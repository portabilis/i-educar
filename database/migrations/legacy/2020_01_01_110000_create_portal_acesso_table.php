<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePortalAcessoTable extends Migration
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
                CREATE SEQUENCE portal.acesso_cod_acesso_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE portal.acesso (
                    cod_acesso integer DEFAULT nextval(\'portal.acesso_cod_acesso_seq\'::regclass) NOT NULL,
                    data_hora timestamp without time zone NOT NULL,
                    ip_externo character varying(50) DEFAULT \'\'::character varying NOT NULL,
                    ip_interno character varying(255) DEFAULT \'\'::character varying NOT NULL,
                    cod_pessoa integer DEFAULT 0 NOT NULL,
                    obs text,
                    sucesso boolean DEFAULT true NOT NULL
                );

                ALTER TABLE ONLY portal.acesso
                    ADD CONSTRAINT acesso_pk PRIMARY KEY (cod_acesso);

                SELECT pg_catalog.setval(\'portal.acesso_cod_acesso_seq\', 19, true);
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
        Schema::dropIfExists('portal.acesso');

        DB::unprepared('DROP SEQUENCE portal.acesso_cod_acesso_seq;');
    }
}
