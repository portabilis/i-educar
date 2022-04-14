<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.frequencia_aluno', function (Blueprint $table) {
            $table->id();
            $table->integer('ref_frequencia');
            $table->integer('ref_cod_matricula');
            $table->char('justificativa');

            $table->foreign('ref_frequencia')
                ->references('id')
                ->on('modules.frequencia')
                ->onDelete('cascade');

            $table->foreign('ref_cod_matricula')
                ->references('cod_matricula')
                ->on('pmieducar.matricula')
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
        Schema::dropIfExists('modules.frequencia_aluno');
    }
}
