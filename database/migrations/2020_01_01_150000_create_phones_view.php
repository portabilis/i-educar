<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreatePhonesView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createView('phones');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('phones');
    }
}
