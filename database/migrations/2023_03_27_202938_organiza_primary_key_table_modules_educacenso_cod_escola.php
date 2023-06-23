<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_escola DROP CONSTRAINT IF EXISTS educacenso_cod_escola_pk;');

        Schema::table('modules.educacenso_cod_escola', function (Blueprint $table) {
            $table->increments('id');
            $table->unique([
                'cod_escola',
                'cod_escola_inep',
            ]);
        });
    }

    public function down()
    {
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_escola DROP CONSTRAINT IF EXISTS educacenso_cod_escola_pk;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_escola DROP CONSTRAINT IF EXISTS educacenso_cod_escola_pkey;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_escola DROP CONSTRAINT IF EXISTS modules_educacenso_cod_escola_cod_escola_cod_escola_inep_unique;');
        Schema::table('modules.educacenso_cod_escola', function (Blueprint $table) {
            $table->dropColumn('id');
        });
        DB::unprepared('ALTER TABLE IF EXISTS modules.educacenso_cod_escola ADD CONSTRAINT educacenso_cod_escola_pk PRIMARY KEY (cod_escola, cod_escola_inep);');
    }
};
