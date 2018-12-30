<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesTabelaArredondamentoTable extends Migration
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
                
                CREATE TABLE modules.tabela_arredondamento (
                    id integer NOT NULL,
                    instituicao_id integer NOT NULL,
                    nome character varying(50) NOT NULL,
                    tipo_nota smallint DEFAULT 1 NOT NULL
                );

                -- ALTER SEQUENCE modules.tabela_arredondamento_id_seq OWNED BY modules.tabela_arredondamento.id;
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
        Schema::dropIfExists('modules.tabela_arredondamento');
    }
}
