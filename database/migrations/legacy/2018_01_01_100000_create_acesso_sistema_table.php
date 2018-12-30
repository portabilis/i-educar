<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAcessoSistemaTable extends Migration
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
                
                CREATE SEQUENCE acesso.sistema_idsis_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE acesso.sistema (
                    idsis integer DEFAULT nextval(\'acesso.sistema_idsis_seq\'::regclass) NOT NULL,
                    nome character varying(60) NOT NULL,
                    descricao character varying(100) NOT NULL,
                    contexto character varying(30) NOT NULL,
                    situacao character(1) NOT NULL,
                    CONSTRAINT ck_sistema_situacao CHECK (((situacao = \'A\'::bpchar) OR (situacao = \'I\'::bpchar)))
                );
                
                ALTER TABLE ONLY acesso.sistema
                    ADD CONSTRAINT pk_sistema PRIMARY KEY (idsis);

                SELECT pg_catalog.setval(\'acesso.sistema_idsis_seq\', 17, true);
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
        Schema::dropIfExists('acesso.sistema');

        DB::unprepared('DROP SEQUENCE acesso.sistema_idsis_seq;');
    }
}
