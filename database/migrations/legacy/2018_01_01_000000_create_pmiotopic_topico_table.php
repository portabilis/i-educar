<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmiotopicTopicoTable extends Migration
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
                
                CREATE TABLE pmiotopic.topico (
                    cod_topico integer DEFAULT nextval(\'pmiotopic.topico_cod_topico_seq\'::regclass) NOT NULL,
                    ref_idpes_cad integer NOT NULL,
                    ref_cod_grupos_cad integer NOT NULL,
                    assunto character varying(255) NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_idpes_exc integer,
                    ref_cod_grupos_exc integer
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
        Schema::dropIfExists('pmiotopic.topico');
    }
}
