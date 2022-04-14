<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DropTableEducacensoEtapaTurma extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('public.educacenso_etapa_turma');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('public.educacenso_etapa_turma', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
        });

        DB::unprepared(
            file_get_contents(database_path('sqls/inserts/public.educacenso_etapa_turma.sql'))
        );
    }
}
