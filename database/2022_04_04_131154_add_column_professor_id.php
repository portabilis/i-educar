<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnProfessorId extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.frequencia', function (Blueprint $table) {
            $table->integer('professor_id');

            $table->foreign('professor_id')
                ->references('cod_servidor')
                ->on('pmieducar.servidor')
                ->onDelete('cascade');
        });

        Schema::table('modules.conteudo_ministrado', function (Blueprint $table) {
            $table->integer('professor_id');

            $table->foreign('professor_id')
                ->references('cod_servidor')
                ->on('pmieducar.servidor')
                ->onDelete('cascade');
        });

        Schema::table('modules.planejamento_aula', function (Blueprint $table) {
            $table->integer('professor_id');

            $table->foreign('professor_id')
                ->references('cod_servidor')
                ->on('pmieducar.servidor')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.frequencia', function (Blueprint $table) {
            $table->dropColumn('professor_id');
        });

        Schema::table('modules.conteudo_ministrado', function (Blueprint $table) {
            $table->dropColumn('professor_id');
        });

        Schema::table('modules.planejamento_aula', function (Blueprint $table) {
            $table->dropColumn('professor_id');
        });
    }
}

