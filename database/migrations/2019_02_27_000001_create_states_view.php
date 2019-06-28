<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateStatesView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('states');
        $this->createView('states');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('states');
    }
}
