<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInEmployeeGraduationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_graduations', function (Blueprint $table) {
            $table->foreign('course_id')->on('modules.educacenso_curso_superior')->references('id');
            $table->foreign('college_id')->on('modules.educacenso_ies')->references('id');
            $table->foreign('discipline_id')->on('employee_graduation_disciplines')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_graduations', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropForeign(['college_id']);
            $table->dropForeign(['discipline_id']);
        });
    }
}
