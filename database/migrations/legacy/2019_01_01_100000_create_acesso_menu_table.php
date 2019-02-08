<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAcessoMenuTable extends Migration
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
                
                CREATE SEQUENCE acesso.menu_idmen_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE acesso.menu (
                    idmen integer DEFAULT nextval(\'acesso.menu_idmen_seq\'::regclass) NOT NULL,
                    idsis integer NOT NULL,
                    menu_idsis integer,
                    menu_idmen integer,
                    nome character varying(40) NOT NULL,
                    descricao character varying(250) NOT NULL,
                    situacao character(1) NOT NULL,
                    ordem numeric(2,0) NOT NULL,
                    CONSTRAINT ck_menu_situacao CHECK (((situacao = \'A\'::bpchar) OR (situacao = \'I\'::bpchar)))
                );
                
                ALTER TABLE ONLY acesso.menu
                    ADD CONSTRAINT pk_menu PRIMARY KEY (idsis, idmen);

                SELECT pg_catalog.setval(\'acesso.menu_idmen_seq\', 1, false);
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
        Schema::dropIfExists('acesso.menu');

        DB::unprepared('DROP SEQUENCE acesso.menu_idmen_seq;');
    }
}
