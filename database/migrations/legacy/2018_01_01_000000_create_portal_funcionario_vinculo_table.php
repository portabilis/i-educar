<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

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
                SET default_with_oids = true;

                CREATE TABLE portal.funcionario_vinculo (
                    cod_funcionario_vinculo integer DEFAULT nextval(\'portal.funcionario_vinculo_cod_funcionario_vinculo_seq\'::regclass) NOT NULL,
                    nm_vinculo character varying(255) DEFAULT \'\'::character varying NOT NULL,
                    abreviatura character varying(16)
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
        Schema::dropIfExists('portal.funcionario_vinculo');
    }
}
