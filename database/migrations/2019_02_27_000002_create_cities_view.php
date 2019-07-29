<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateCitiesView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('cities');
        $this->createView('cities');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('cities');
    }
}
