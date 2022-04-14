<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateReligionsView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('religions');
        $this->createView('religions');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('religions');
    }
}
