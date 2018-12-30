<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarBibliotecaTable extends Migration
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
                
                CREATE TABLE pmieducar.biblioteca (
                    cod_biblioteca integer DEFAULT nextval(\'pmieducar.biblioteca_cod_biblioteca_seq\'::regclass) NOT NULL,
                    ref_cod_instituicao integer,
                    ref_cod_escola integer,
                    nm_biblioteca character varying(255) NOT NULL,
                    valor_multa double precision,
                    max_emprestimo integer,
                    valor_maximo_multa double precision,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    requisita_senha smallint DEFAULT (0)::smallint NOT NULL,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    dias_espera numeric(2,0),
                    tombo_automatico boolean DEFAULT true,
                    bloqueia_emprestimo_em_atraso boolean
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
        Schema::dropIfExists('pmieducar.biblioteca');
    }
}
