<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalJorEdicaoTable extends Migration
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

                CREATE TABLE portal.jor_edicao (
                    cod_jor_edicao integer DEFAULT nextval(\'portal.jor_edicao_cod_jor_edicao_seq\'::regclass) NOT NULL,
                    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
                    jor_ano_edicao character varying(5) DEFAULT \'\'::character varying NOT NULL,
                    jor_edicao integer DEFAULT 0 NOT NULL,
                    jor_dt_inicial date NOT NULL,
                    jor_dt_final date,
                    jor_extra smallint DEFAULT (0)::smallint
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
        Schema::dropIfExists('portal.jor_edicao');
    }
}
