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
                
                CREATE SEQUENCE modules.tabela_arredondamento_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.tabela_arredondamento (
                    id integer NOT NULL,
                    instituicao_id integer NOT NULL,
                    nome character varying(50) NOT NULL,
                    tipo_nota smallint DEFAULT 1 NOT NULL
                );

                ALTER SEQUENCE modules.tabela_arredondamento_id_seq OWNED BY modules.tabela_arredondamento.id;
                
                ALTER TABLE ONLY modules.tabela_arredondamento ALTER COLUMN id SET DEFAULT nextval(\'modules.tabela_arredondamento_id_seq\'::regclass);
                
                SELECT pg_catalog.setval(\'modules.tabela_arredondamento_id_seq\', 2, true);
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
