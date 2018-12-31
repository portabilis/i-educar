<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesFaltaGeralTable extends Migration
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
                
                CREATE SEQUENCE modules.falta_geral_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.falta_geral (
                    id integer NOT NULL,
                    falta_aluno_id integer NOT NULL,
                    quantidade integer DEFAULT 0,
                    etapa character varying(2) NOT NULL
                );

                ALTER SEQUENCE modules.falta_geral_id_seq OWNED BY modules.falta_geral.id;
                
                ALTER TABLE ONLY modules.falta_geral
                    ADD CONSTRAINT falta_geral_pkey PRIMARY KEY (falta_aluno_id, etapa);

                ALTER TABLE ONLY modules.falta_geral ALTER COLUMN id SET DEFAULT nextval(\'modules.falta_geral_id_seq\'::regclass);
                
                CREATE INDEX idx_falta_geral_falta_aluno_id ON modules.falta_geral USING btree (falta_aluno_id);

                SELECT pg_catalog.setval(\'modules.falta_geral_id_seq\', 1, false);
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
        Schema::dropIfExists('modules.falta_geral');
    }
}
