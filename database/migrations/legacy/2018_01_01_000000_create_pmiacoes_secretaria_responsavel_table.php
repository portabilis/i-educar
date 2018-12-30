<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmiacoesSecretariaResponsavelTable extends Migration
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
                
                CREATE TABLE pmiacoes.secretaria_responsavel (
                    ref_cod_setor integer NOT NULL,
                    ref_cod_funcionario_cad integer NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL
                );
                
                ALTER TABLE ONLY pmiacoes.secretaria_responsavel
                    ADD CONSTRAINT secretaria_responsavel_pkey PRIMARY KEY (ref_cod_setor);
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
        Schema::dropIfExists('pmiacoes.secretaria_responsavel');
    }
}
