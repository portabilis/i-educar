<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesComponenteCurricularAnoEscolarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        # FIXME

        DB::unprepared(
            '
                SET default_with_oids = false;

                CREATE SEQUENCE modules.componente_curricular_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.componente_curricular_ano_escolar (
                    componente_curricular_id integer NOT NULL,
                    ano_escolar_id integer NOT NULL,
                    carga_horaria numeric(7,3),
                    tipo_nota integer,
                    anos_letivos smallint[] DEFAULT \'{}\'::smallint[] NOT NULL
                );

                ALTER SEQUENCE modules.componente_curricular_id_seq OWNED BY modules.componente_curricular.id;
                
                ALTER TABLE ONLY modules.componente_curricular ALTER COLUMN id SET DEFAULT nextval(\'modules.componente_curricular_id_seq\'::regclass);
                
                SELECT pg_catalog.setval(\'modules.componente_curricular_id_seq\', 2, true);
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
        Schema::dropIfExists('modules.componente_curricular_ano_escolar');
    }
}
