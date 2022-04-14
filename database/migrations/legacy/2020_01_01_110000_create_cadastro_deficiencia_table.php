<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCadastroDeficienciaTable extends Migration
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
                CREATE SEQUENCE cadastro.deficiencia_cod_deficiencia_seq
                    START WITH 15
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE cadastro.deficiencia (
                    cod_deficiencia integer DEFAULT nextval(\'cadastro.deficiencia_cod_deficiencia_seq\'::regclass) NOT NULL,
                    nm_deficiencia character varying(70) NOT NULL,
                    deficiencia_educacenso smallint,
                    desconsidera_regra_diferenciada boolean DEFAULT false,
	                updated_at timestamp NULL DEFAULT now()
                );

                ALTER TABLE ONLY cadastro.deficiencia
                    ADD CONSTRAINT pk_cadastro_escolaridade PRIMARY KEY (cod_deficiencia);

                SELECT pg_catalog.setval(\'cadastro.deficiencia_cod_deficiencia_seq\', 15, false);
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
        Schema::dropIfExists('cadastro.deficiencia');

        DB::unprepared('DROP SEQUENCE cadastro.deficiencia_cod_deficiencia_seq;');
    }
}
