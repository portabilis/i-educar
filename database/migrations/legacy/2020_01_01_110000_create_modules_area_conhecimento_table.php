<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateModulesAreaConhecimentoTable extends Migration
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
                CREATE SEQUENCE modules.area_conhecimento_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.area_conhecimento (
                    id integer NOT NULL,
                    instituicao_id integer NOT NULL,
                    nome character varying(200) NOT NULL,
                    secao character varying(50),
                    ordenamento_ac integer DEFAULT 99999,
	                updated_at timestamp NULL DEFAULT now()
                );

                ALTER SEQUENCE modules.area_conhecimento_id_seq OWNED BY modules.area_conhecimento.id;

                ALTER TABLE ONLY modules.area_conhecimento
                    ADD CONSTRAINT area_conhecimento_pkey PRIMARY KEY (id, instituicao_id);

                ALTER TABLE ONLY modules.area_conhecimento ALTER COLUMN id SET DEFAULT nextval(\'modules.area_conhecimento_id_seq\'::regclass);

                CREATE INDEX area_conhecimento_nome_key ON modules.area_conhecimento USING btree (nome);

                SELECT pg_catalog.setval(\'modules.area_conhecimento_id_seq\', 2, true);
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
        Schema::dropIfExists('modules.area_conhecimento');
    }
}
