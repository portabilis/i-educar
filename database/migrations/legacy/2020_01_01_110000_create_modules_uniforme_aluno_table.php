<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesUniformeAlunoTable extends Migration
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

                CREATE TABLE modules.uniforme_aluno (
                    ref_cod_aluno integer NOT NULL,
                    recebeu_uniforme character(1),
                    quantidade_camiseta integer,
                    tamanho_camiseta character(2),
                    quantidade_blusa_jaqueta integer,
                    tamanho_blusa_jaqueta character(2),
                    quantidade_bermuda integer,
                    tamanho_bermuda character(2),
                    quantidade_calca integer,
                    tamanho_calca character(2),
                    quantidade_saia integer,
                    tamanho_saia character(2),
                    quantidade_calcado integer,
                    tamanho_calcado character(2),
                    quantidade_meia integer,
                    tamanho_meia character(2)
                );
                
                ALTER TABLE ONLY modules.uniforme_aluno
                    ADD CONSTRAINT uniforme_aluno_pkey PRIMARY KEY (ref_cod_aluno);
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
        Schema::dropIfExists('modules.uniforme_aluno');
    }
}
