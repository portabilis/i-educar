<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmicontrolesisSistemaTable extends Migration
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
                
                CREATE TABLE pmicontrolesis.sistema (
                    cod_sistema integer DEFAULT nextval(\'pmicontrolesis.sistema_cod_sistema_seq\'::regclass) NOT NULL,
                    nm_sistema character varying(255) NOT NULL,
                    ref_cod_funcionario_cad integer NOT NULL,
                    ref_cod_funcionario_exc integer,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint
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
        Schema::dropIfExists('pmicontrolesis.sistema');
    }
}
