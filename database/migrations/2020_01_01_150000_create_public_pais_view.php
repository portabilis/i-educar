<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreatePublicPaisView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createView('public.pais');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('public.pais');
    }
}
