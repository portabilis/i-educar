<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesDocenteLicenciaturaTable extends Migration
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

                CREATE SEQUENCE modules.docente_licenciatura_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.docente_licenciatura (
                    id integer NOT NULL,
                    servidor_id integer NOT NULL,
                    licenciatura integer NOT NULL,
                    curso_id integer,
                    ano_conclusao integer NOT NULL,
                    ies_id integer,
                    user_id integer NOT NULL,
                    created_at timestamp without time zone NOT NULL,
                    updated_at timestamp without time zone
                );

                ALTER SEQUENCE modules.docente_licenciatura_id_seq OWNED BY modules.docente_licenciatura.id;
                
                ALTER TABLE ONLY modules.docente_licenciatura
                    ADD CONSTRAINT docente_licenciatura_curso_unique UNIQUE (servidor_id, curso_id, ies_id);

                ALTER TABLE ONLY modules.docente_licenciatura
                    ADD CONSTRAINT docente_licenciatura_pk PRIMARY KEY (id);

                ALTER TABLE ONLY modules.docente_licenciatura ALTER COLUMN id SET DEFAULT nextval(\'modules.docente_licenciatura_id_seq\'::regclass);
                
                CREATE INDEX docente_licenciatura_ies_idx ON modules.docente_licenciatura USING btree (ies_id);

                SELECT pg_catalog.setval(\'modules.docente_licenciatura_id_seq\', 1, false);
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
        Schema::dropIfExists('modules.docente_licenciatura');
    }
}
