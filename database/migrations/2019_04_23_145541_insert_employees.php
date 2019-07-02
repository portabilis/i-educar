<?php

use iEducar\Modules\Educacenso\Migrations\InsertEmployees as InsertEmployeesClass;
use Illuminate\Database\Migrations\Migration;

class InsertEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        InsertEmployeesClass::execute();
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
