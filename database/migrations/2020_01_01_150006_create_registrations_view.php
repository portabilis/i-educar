<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateRegistrationsView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('registrations');
        $this->createView('registrations');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('registrations');
    }
}
