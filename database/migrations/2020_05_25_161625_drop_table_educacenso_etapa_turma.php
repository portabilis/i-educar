<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
        Schema::create('public.etapa_educacenso', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
        });

        DB::unprepared(
            file_get_contents(database_path("sqls/inserts/public.etapa_educacenso.sql"))
        );
    }
}
