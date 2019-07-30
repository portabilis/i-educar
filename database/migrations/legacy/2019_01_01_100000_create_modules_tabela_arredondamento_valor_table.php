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
        DB::unprepared(
            '
                SET default_with_oids = false;

                CREATE SEQUENCE modules.tabela_arredondamento_valor_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.tabela_arredondamento_valor (
                    id integer NOT NULL,
                    tabela_arredondamento_id integer NOT NULL,
                    nome character varying(5) NOT NULL,
                    descricao character varying(25),
                    valor_minimo numeric(5,3),
                    valor_maximo numeric(5,3),
                    casa_decimal_exata smallint,
                    acao smallint,
                    observacao varchar(191) NULL
                );

                ALTER SEQUENCE modules.tabela_arredondamento_valor_id_seq OWNED BY modules.tabela_arredondamento_valor.id;
                
                ALTER TABLE ONLY modules.tabela_arredondamento_valor
                    ADD CONSTRAINT tabela_arredondamento_valor_pkey PRIMARY KEY (id);

                ALTER TABLE ONLY modules.tabela_arredondamento_valor ALTER COLUMN id SET DEFAULT nextval(\'modules.tabela_arredondamento_valor_id_seq\'::regclass);
                
                CREATE INDEX idx_tabela_arredondamento_valor_tabela_id ON modules.tabela_arredondamento_valor USING btree (tabela_arredondamento_id);

                SELECT pg_catalog.setval(\'modules.tabela_arredondamento_valor_id_seq\', 26, true);
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
