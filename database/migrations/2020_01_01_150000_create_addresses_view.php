<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createView('addresses');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('addresses');
    }
}
