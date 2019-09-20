<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

class OrdenaMenuConsultaDispensaDisciplinas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Menu::where('process', Process::EXEMPTION_LIST)->first()->update(['order' => 1]);
        Menu::where('process', 9998900)->first()->update(['order' => 2]);
        Menu::where('process', 9998910)->first()->update(['order' => 3]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Menu::where('process', Process::EXEMPTION_LIST)->first()->update(['order' => 0]);
        Menu::where('process', 9998900)->first()->update(['order' => 0]);
        Menu::where('process', 9998910)->first()->update(['order' => 0]);
    }
}
