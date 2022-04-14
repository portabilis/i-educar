<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreatePeopleView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('persons');
        $this->createView('persons');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('persons');
    }
}
