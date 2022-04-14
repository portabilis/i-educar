<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateModulesNotaComponenteCurricularTable extends Migration
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
                CREATE SEQUENCE modules.nota_componente_curricular_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.nota_componente_curricular (
                    id integer NOT NULL,
                    nota_aluno_id integer NOT NULL,
                    componente_curricular_id integer NOT NULL,
                    nota numeric(8,4) DEFAULT 0,
                    nota_arredondada character varying(10) DEFAULT 0,
                    etapa character varying(2) NOT NULL,
                    nota_recuperacao character varying(10),
                    nota_original character varying(10),
                    nota_recuperacao_especifica character varying(10)
                );

                ALTER SEQUENCE modules.nota_componente_curricular_id_seq OWNED BY modules.nota_componente_curricular.id;

                ALTER TABLE ONLY modules.nota_componente_curricular
                    ADD CONSTRAINT nota_componente_curricular_pkey PRIMARY KEY (nota_aluno_id, componente_curricular_id, etapa);

                ALTER TABLE ONLY modules.nota_componente_curricular ALTER COLUMN id SET DEFAULT nextval(\'modules.nota_componente_curricular_id_seq\'::regclass);

                CREATE INDEX idx_nota_componente_curricular_etapa ON modules.nota_componente_curricular USING btree (nota_aluno_id, componente_curricular_id, etapa);

                CREATE INDEX idx_nota_componente_curricular_etp ON modules.nota_componente_curricular USING btree (componente_curricular_id, etapa);

                CREATE INDEX idx_nota_componente_curricular_id ON modules.nota_componente_curricular USING btree (componente_curricular_id);

                SELECT pg_catalog.setval(\'modules.nota_componente_curricular_id_seq\', 1, true);
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
        Schema::dropIfExists('modules.nota_componente_curricular');
    }
}
