<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreatePublicBairroView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createView('public.bairro');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('public.bairro');
    }
}
