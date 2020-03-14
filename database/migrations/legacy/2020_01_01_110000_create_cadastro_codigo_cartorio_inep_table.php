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
        DB::unprepared(
            '
                SET default_with_oids = false;

                CREATE SEQUENCE cadastro.codigo_cartorio_inep_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE cadastro.codigo_cartorio_inep (
                    id integer NOT NULL,
                    id_cartorio integer NOT NULL,
                    descricao character varying,
                    cod_serventia integer,
                    cod_municipio integer,
                    ref_sigla_uf character varying(3)
                );
                
                ALTER SEQUENCE cadastro.codigo_cartorio_inep_id_seq OWNED BY cadastro.codigo_cartorio_inep.id;
                
                ALTER TABLE ONLY cadastro.codigo_cartorio_inep
                    ADD CONSTRAINT pk_id PRIMARY KEY (id);

                ALTER TABLE ONLY cadastro.codigo_cartorio_inep ALTER COLUMN id SET DEFAULT nextval(\'cadastro.codigo_cartorio_inep_id_seq\'::regclass);
                
                SELECT pg_catalog.setval(\'cadastro.codigo_cartorio_inep_id_seq\', 14212, true);
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
