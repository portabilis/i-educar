<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_aluno DROP CONSTRAINT IF EXISTS educacenso_cod_aluno_pk;');

        Schema::table('modules.educacenso_cod_aluno', function (Blueprint $table) {
            $table->increments('id');
            $table->unique([
                'cod_aluno',
                'cod_aluno_inep',
            ]);
        });
    }

    public function down()
    {
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_aluno DROP CONSTRAINT IF EXISTS educacenso_cod_aluno_pk;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_aluno DROP CONSTRAINT IF EXISTS educacenso_cod_aluno_pkey;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_aluno DROP CONSTRAINT IF EXISTS educacenso_cod_aluno_cod_aluno_cod_aluno_inep_unique;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_aluno DROP CONSTRAINT IF EXISTS modules_educacenso_cod_aluno_cod_aluno_cod_aluno_inep_unique;');
        Schema::table('modules.educacenso_cod_aluno', function (Blueprint $table) {
            $table->dropColumn('id');
        });
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_aluno ADD CONSTRAINT educacenso_cod_aluno_pk PRIMARY KEY (cod_aluno, cod_aluno_inep);');
    }
};
