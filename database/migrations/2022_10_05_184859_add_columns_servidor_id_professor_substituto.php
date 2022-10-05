<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsServidorIdProfessorSubstituto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.planejamento_aula', function (Blueprint $table) {
            $table->integer('servidor_id')->nullable();
        });

        Schema::table('modules.frequencia', function (Blueprint $table) {
            $table->integer('servidor_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
