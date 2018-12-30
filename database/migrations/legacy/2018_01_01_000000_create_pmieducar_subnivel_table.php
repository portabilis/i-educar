<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarSubnivelTable extends Migration
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
                SET default_with_oids = false;

                CREATE TABLE pmieducar.subnivel (
                    cod_subnivel integer DEFAULT nextval(\'pmieducar.subnivel_cod_subnivel_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_subnivel_anterior integer,
                    ref_cod_nivel integer NOT NULL,
                    nm_subnivel character varying(100),
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo boolean DEFAULT true NOT NULL,
                    salario double precision NOT NULL
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
        Schema::dropIfExists('pmieducar.subnivel');
    }
}
