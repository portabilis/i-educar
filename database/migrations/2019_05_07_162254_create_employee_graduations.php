<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeGraduations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_graduations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->unsigned();
            $table->integer('course_id')->unsigned();
            $table->integer('completion_year');
            $table->integer('college_id')->unsigned();
            $table->integer('discipline_id')->unsigned()->nullable();

            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('modules.educacenso_curso_superior');
            $table->foreign('college_id')->references('id')->on('modules.educacenso_ies');
            $table->foreign('discipline_id')->references('id')->on('employee_graduation_disciplines');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_graduations');
    }
}
