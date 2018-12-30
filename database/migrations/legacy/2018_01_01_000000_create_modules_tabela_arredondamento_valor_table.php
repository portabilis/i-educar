<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesTabelaArredondamentoValorTable extends Migration
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

                CREATE TABLE modules.tabela_arredondamento_valor (
                    id integer NOT NULL,
                    tabela_arredondamento_id integer NOT NULL,
                    nome character varying(5) NOT NULL,
                    descricao character varying(25),
                    valor_minimo numeric(5,3),
                    valor_maximo numeric(5,3),
                    casa_decimal_exata smallint,
                    acao smallint
                );

                -- ALTER SEQUENCE modules.tabela_arredondamento_valor_id_seq OWNED BY modules.tabela_arredondamento_valor.id;
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
        Schema::dropIfExists('modules.tabela_arredondamento_valor');
    }
}
