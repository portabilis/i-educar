<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarAcervoEditoraTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.acervo_editora_cod_acervo_editora_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.acervo_editora (
                    cod_acervo_editora integer DEFAULT nextval(\'pmieducar.acervo_editora_cod_acervo_editora_seq\'::regclass) NOT NULL,
                    ref_usuario_cad integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_idtlog character varying(20),
                    ref_sigla_uf character(2),
                    nm_editora character varying(255) NOT NULL,
                    cep numeric(8,0),
                    cidade character varying(60),
                    bairro character varying(60),
                    logradouro character varying(255),
                    numero numeric(6,0),
                    telefone integer,
                    ddd_telefone numeric(3,0),
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_biblioteca integer
                );
                
                ALTER TABLE ONLY pmieducar.acervo_editora
                    ADD CONSTRAINT acervo_editora_pkey PRIMARY KEY (cod_acervo_editora);

                SELECT pg_catalog.setval(\'pmieducar.acervo_editora_cod_acervo_editora_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.acervo_editora');

        DB::unprepared('DROP SEQUENCE pmieducar.acervo_editora_cod_acervo_editora_seq;');
    }
}
