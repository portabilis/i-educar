<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmiotopicGrupopessoaTable extends Migration
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
                
                CREATE TABLE pmiotopic.grupopessoa (
                    ref_idpes integer NOT NULL,
                    ref_cod_grupos integer NOT NULL,
                    ref_grupos_exc integer,
                    ref_pessoa_exc integer,
                    ref_grupos_cad integer,
                    ref_pessoa_cad integer,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_auxiliar_cad integer,
                    ref_ref_cod_atendimento_cad integer
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
        Schema::dropIfExists('pmiotopic.grupopessoa');
    }
}
