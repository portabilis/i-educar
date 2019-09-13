<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateCensusStudentsView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('census_students');
        $this->createView('census_students');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('census_students');
    }
}
