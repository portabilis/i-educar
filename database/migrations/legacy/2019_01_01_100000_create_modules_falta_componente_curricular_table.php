<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesFaltaComponenteCurricularTable extends Migration
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
                
                CREATE SEQUENCE modules.falta_componente_curricular_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.falta_componente_curricular (
                    id integer NOT NULL,
                    falta_aluno_id integer NOT NULL,
                    componente_curricular_id integer NOT NULL,
                    quantidade integer DEFAULT 0,
                    etapa character varying(2) NOT NULL
                );

                ALTER SEQUENCE modules.falta_componente_curricular_id_seq OWNED BY modules.falta_componente_curricular.id;
                
                ALTER TABLE ONLY modules.falta_componente_curricular
                    ADD CONSTRAINT falta_componente_curricular_pkey PRIMARY KEY (falta_aluno_id, componente_curricular_id, etapa);

                ALTER TABLE ONLY modules.falta_componente_curricular ALTER COLUMN id SET DEFAULT nextval(\'modules.falta_componente_curricular_id_seq\'::regclass);
                
                CREATE INDEX idx_falta_componente_curricular_id1 ON modules.falta_componente_curricular USING btree (falta_aluno_id, componente_curricular_id, etapa);

                SELECT pg_catalog.setval(\'modules.falta_componente_curricular_id_seq\', 1, true);
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
        Schema::dropIfExists('modules.falta_componente_curricular');
    }
}
