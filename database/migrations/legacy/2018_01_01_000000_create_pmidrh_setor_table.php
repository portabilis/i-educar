<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmidrhSetorTable extends Migration
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
                
                CREATE TABLE pmidrh.setor (
                    cod_setor integer DEFAULT nextval(\'pmidrh.setor_cod_setor_seq\'::regclass) NOT NULL,
                    ref_cod_pessoa_exc integer,
                    ref_cod_pessoa_cad integer NOT NULL,
                    ref_cod_setor integer,
                    nm_setor character varying(255) NOT NULL,
                    sgl_setor character varying(15) NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    nivel smallint DEFAULT (1)::smallint NOT NULL,
                    no_paco smallint DEFAULT 1,
                    endereco text,
                    tipo character(1),
                    ref_idpes_resp integer
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
        Schema::dropIfExists('pmidrh.setor');
    }
}
