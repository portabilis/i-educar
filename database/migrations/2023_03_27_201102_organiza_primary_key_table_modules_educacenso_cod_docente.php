<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_docente DROP CONSTRAINT IF EXISTS educacenso_cod_docente_pk;');

        Schema::table('modules.educacenso_cod_docente', function (Blueprint $table) {
            $table->increments('id');
            $table->unique([
                'cod_servidor',
                'cod_docente_inep',
            ]);
        });
    }

    public function down()
    {
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_docente DROP CONSTRAINT IF EXISTS educacenso_cod_docente_pk;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_docente DROP CONSTRAINT IF EXISTS educacenso_cod_docente_pkey;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_docente DROP CONSTRAINT IF EXISTS modules_educacenso_cod_docente_cod_servidor_cod_docente_inep_un;');
        Schema::table('modules.educacenso_cod_docente', function (Blueprint $table) {
            $table->dropColumn('id');
        });
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_docente ADD CONSTRAINT educacenso_cod_docente_pk PRIMARY KEY (cod_servidor, cod_docente_inep);');
    }
};
