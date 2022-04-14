<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_managers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->unsigned();
            $table->integer('school_id')->unsigned();
            $table->integer('role_id')->nullable()->unsigned();
            $table->integer('access_criteria_id')->nullable()->unsigned();
            $table->string('access_criteria_description')->nullable();
            $table->integer('link_type_id')->nullable()->unsigned();
            $table->boolean('chief')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_managers');
    }
}
