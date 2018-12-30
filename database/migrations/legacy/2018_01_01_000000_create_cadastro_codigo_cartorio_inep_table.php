<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateCadastroCodigoCartorioInepTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        # FIXME
        
        DB::unprepared(
            '
                SET default_with_oids = false;

                CREATE TABLE cadastro.codigo_cartorio_inep (
                    id integer NOT NULL,
                    id_cartorio integer NOT NULL,
                    descricao character varying,
                    cod_serventia integer,
                    cod_municipio integer,
                    ref_sigla_uf character varying(3)
                );
                
                -- ALTER SEQUENCE cadastro.codigo_cartorio_inep_id_seq OWNED BY cadastro.codigo_cartorio_inep.id;
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
        Schema::dropIfExists('cadastro.codigo_cartorio_inep');
    }
}
