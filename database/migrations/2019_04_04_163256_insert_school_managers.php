<?php

use iEducar\Modules\Educacenso\Migrations\InsertSchoolManagers as InsertSchoolManagersClass;
use Illuminate\Database\Migrations\Migration;

class InsertSchoolManagers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        InsertSchoolManagersClass::execute();
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
