<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmiacoesCategoriaTable extends Migration
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
                
                CREATE TABLE pmiacoes.categoria (
                    cod_categoria integer DEFAULT nextval(\'pmiacoes.categoria_cod_categoria_seq\'::regclass) NOT NULL,
                    ref_funcionario_exc integer,
                    ref_funcionario_cad integer NOT NULL,
                    nm_categoria character varying(255) NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
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
        Schema::dropIfExists('pmiacoes.categoria');
    }
}
