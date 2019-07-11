<?php

use iEducar\Modules\Educacenso\Migrations\InsertEmployeeGraduations as InsertEmployeeGraduationsClass;
use Illuminate\Database\Migrations\Migration;

class InsertEmployeeGraduations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        InsertEmployeeGraduationsClass::execute();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
