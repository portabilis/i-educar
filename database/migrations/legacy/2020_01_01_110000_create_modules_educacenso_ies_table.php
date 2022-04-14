<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateModulesEducacensoIesTable extends Migration
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
                CREATE SEQUENCE modules.educacenso_ies_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.educacenso_ies (
                    id integer NOT NULL,
                    ies_id integer NOT NULL,
                    nome character varying(255) NOT NULL,
                    dependencia_administrativa_id integer NOT NULL,
                    tipo_instituicao_id integer NOT NULL,
                    uf character(2),
                    user_id integer NOT NULL,
                    created_at timestamp without time zone NOT NULL,
                    updated_at timestamp without time zone
                );

                ALTER SEQUENCE modules.educacenso_ies_id_seq OWNED BY modules.educacenso_ies.id;

                ALTER TABLE ONLY modules.educacenso_ies
                    ADD CONSTRAINT educacenso_ies_pk PRIMARY KEY (id);

                ALTER TABLE ONLY modules.educacenso_ies ALTER COLUMN id SET DEFAULT nextval(\'modules.educacenso_ies_id_seq\'::regclass);

                CREATE INDEX idx_educacenso_ies_ies_id ON modules.educacenso_ies USING btree (ies_id);

                SELECT pg_catalog.setval(\'modules.educacenso_ies_id_seq\', 6179, true);
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
        Schema::dropIfExists('modules.educacenso_ies');
    }
}
