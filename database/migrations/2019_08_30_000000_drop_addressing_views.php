<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class DropAddressingViews extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('persons_has_places');
        $this->dropView('places');
        $this->dropView('cities');
        $this->dropView('states');
        $this->dropView('countries');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->createView('countries');
        $this->createView('states');
        $this->createView('cities');
        $this->createView('places');
        $this->createView('persons_has_places');
    }
}
