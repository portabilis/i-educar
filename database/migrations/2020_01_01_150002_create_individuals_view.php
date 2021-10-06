<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateIndividualsView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('individuals');
        $this->createView('individuals');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('individuals');
    }
}
