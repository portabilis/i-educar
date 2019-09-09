<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreatePersonHasPlacesView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createView('persons_has_places');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('persons_has_places');
    }
}
