<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

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
                SET default_with_oids = false;

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
                    ordenamento_ac integer DEFAULT 99999
                );
                
                ALTER SEQUENCE modules.area_conhecimento_id_seq OWNED BY modules.area_conhecimento.id;
                
                ALTER TABLE ONLY modules.area_conhecimento ALTER COLUMN id SET DEFAULT nextval(\'modules.area_conhecimento_id_seq\'::regclass);
                
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
