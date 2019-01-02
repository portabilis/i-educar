<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesParecerGeralTable extends Migration
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
                
                CREATE SEQUENCE modules.parecer_geral_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.parecer_geral (
                    id integer NOT NULL,
                    parecer_aluno_id integer NOT NULL,
                    parecer text,
                    etapa character varying(2) NOT NULL
                );

                ALTER SEQUENCE modules.parecer_geral_id_seq OWNED BY modules.parecer_geral.id;

                ALTER TABLE ONLY modules.parecer_geral
                    ADD CONSTRAINT parecer_geral_pkey PRIMARY KEY (parecer_aluno_id, etapa);

                ALTER TABLE ONLY modules.parecer_geral ALTER COLUMN id SET DEFAULT nextval(\'modules.parecer_geral_id_seq\'::regclass);
                
                CREATE INDEX idx_parecer_geral_parecer_aluno_etp ON modules.parecer_geral USING btree (parecer_aluno_id, etapa);

                SELECT pg_catalog.setval(\'modules.parecer_geral_id_seq\', 1, false);
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
        Schema::dropIfExists('modules.parecer_geral');
    }
}
