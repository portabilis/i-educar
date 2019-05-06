<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSchoolManagers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('school_managers', function (Blueprint $table) {
            $table->renameColumn('individual_id', 'employee_id');
            $table->dropColumn('inep_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('school_managers', function (Blueprint $table) {
            $table->renameColumn('employee_id', 'individual_id');
            $table->string('inep_id', 12)->nullable();
        });

    }
}
