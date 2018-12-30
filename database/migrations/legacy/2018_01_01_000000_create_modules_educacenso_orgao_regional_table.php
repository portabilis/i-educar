<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesEducacensoOrgaoRegionalTable extends Migration
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
                
                CREATE TABLE modules.educacenso_orgao_regional (
                    sigla_uf character varying(2) NOT NULL,
                    codigo character varying(5) NOT NULL
                );
                
                ALTER TABLE ONLY modules.educacenso_orgao_regional
                    ADD CONSTRAINT pk_educacenso_orgao_regional PRIMARY KEY (sigla_uf, codigo);
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
        Schema::dropIfExists('modules.educacenso_orgao_regional');
    }
}
