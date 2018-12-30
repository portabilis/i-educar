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
                
                CREATE TABLE acesso.sistema (
                    idsis integer DEFAULT nextval(\'acesso.sistema_idsis_seq\'::regclass) NOT NULL,
                    nome character varying(60) NOT NULL,
                    descricao character varying(100) NOT NULL,
                    contexto character varying(30) NOT NULL,
                    situacao character(1) NOT NULL,
                    CONSTRAINT ck_sistema_situacao CHECK (((situacao = \'A\'::bpchar) OR (situacao = \'I\'::bpchar)))
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
        Schema::dropIfExists('acesso.sistema');
    }
}
