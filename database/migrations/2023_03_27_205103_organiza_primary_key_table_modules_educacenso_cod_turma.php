<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_turma DROP CONSTRAINT IF EXISTS educacenso_cod_turma_pk;');

        Schema::table('modules.educacenso_cod_turma', function (Blueprint $table) {
            $table->increments('id');
            $table->unique([
                'cod_turma',
                'cod_turma_inep',
            ]);
        });
    }

    public function down()
    {
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_turma DROP CONSTRAINT IF EXISTS educacenso_cod_turma_pk;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_turma DROP CONSTRAINT IF EXISTS educacenso_cod_turma_pkey;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_turma DROP CONSTRAINT IF EXISTS modules_educacenso_cod_turma_cod_turma_cod_turma_inep_unique;');
        Schema::table('modules.educacenso_cod_turma', function (Blueprint $table) {
            $table->dropColumn('id');
        });
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_turma ADD CONSTRAINT educacenso_cod_turma_pk PRIMARY KEY (cod_turma, cod_turma_inep);');
    }
};
