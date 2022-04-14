<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateModulesTabelaArredondamentoTable extends Migration
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
                    tipo_nota smallint DEFAULT 1 NOT NULL,
	                updated_at timestamp NULL DEFAULT now(),
	                arredondar_nota int2 NOT NULL DEFAULT \'0\'::smallint
                );

                ALTER SEQUENCE modules.tabela_arredondamento_id_seq OWNED BY modules.tabela_arredondamento.id;

                ALTER TABLE ONLY modules.tabela_arredondamento
                    ADD CONSTRAINT tabela_arredondamento_pkey PRIMARY KEY (id, instituicao_id);

                ALTER TABLE ONLY modules.tabela_arredondamento ALTER COLUMN id SET DEFAULT nextval(\'modules.tabela_arredondamento_id_seq\'::regclass);

                CREATE UNIQUE INDEX tabela_arredondamento_id_key ON modules.tabela_arredondamento USING btree (id);

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
