<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInSchoolManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('school_managers', function (Blueprint $table) {
            $table->foreign('school_id')->on('pmieducar.escola')->references('cod_escola');
            $table->foreign('role_id')->on('manager_roles')->references('id');
            $table->foreign('access_criteria_id')->on('manager_access_criterias')->references('id');
            $table->foreign('link_type_id')->on('manager_link_types')->references('id');
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
            $table->dropForeign(['school_id']);
            $table->dropForeign(['role_id']);
            $table->dropForeign(['access_criteria_id']);
            $table->dropForeign(['link_type_id']);
        });
    }
}
