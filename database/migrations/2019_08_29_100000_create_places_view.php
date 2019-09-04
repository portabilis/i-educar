<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreatePlacesView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createView('places');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('places');
    }
}
