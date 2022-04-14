<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('students');
        $this->createView('students');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('students');
    }
}
