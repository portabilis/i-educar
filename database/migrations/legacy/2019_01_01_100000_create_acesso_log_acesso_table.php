<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAcessoLogAcessoTable extends Migration
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
                
                CREATE TABLE acesso.log_acesso (
                    data timestamp without time zone NOT NULL,
                    idpes numeric(8,0) NOT NULL,
                    idsis integer,
                    idins integer,
                    idcli character varying(10),
                    operacao character(1) NOT NULL,
                    CONSTRAINT ck_log_acesso_situacao CHECK (((operacao = \'I\'::bpchar) OR (operacao = \'O\'::bpchar)))
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
        Schema::dropIfExists('acesso.log_acesso');
    }
}
