<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmiotopicFuncionarioSuTable extends Migration
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
                SET default_with_oids = true;
                
                CREATE TABLE pmiotopic.funcionario_su (
                    ref_ref_cod_pessoa_fj integer NOT NULL
                );
                
                ALTER TABLE ONLY pmiotopic.funcionario_su
                    ADD CONSTRAINT funcionario_su_pkey PRIMARY KEY (ref_ref_cod_pessoa_fj);
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
        Schema::dropIfExists('pmiotopic.funcionario_su');
    }
}
