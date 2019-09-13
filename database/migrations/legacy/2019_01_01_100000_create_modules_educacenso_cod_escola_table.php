<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesEducacensoCodEscolaTable extends Migration
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

                CREATE TABLE modules.educacenso_cod_escola (
                    cod_escola integer NOT NULL,
                    cod_escola_inep bigint NOT NULL,
                    nome_inep character varying(255),
                    fonte character varying(255),
                    created_at timestamp without time zone NOT NULL,
                    updated_at timestamp without time zone
                );
                
                ALTER TABLE ONLY modules.educacenso_cod_escola
                    ADD CONSTRAINT educacenso_cod_escola_pk PRIMARY KEY (cod_escola, cod_escola_inep);

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
        Schema::dropIfExists('modules.educacenso_cod_escola');
    }
}
