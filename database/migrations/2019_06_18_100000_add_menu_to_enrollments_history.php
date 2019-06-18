<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

class AddMenuToEnrollmentsHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Menu::query()->create([
            'parent_id' => Menu::query()->where('process', Process::MENU_SCHOOL)->firstOrFail()->getKey(),
            'title' => 'Histórico de enturmações da matrícula',
            'description',
            'type' => 2,
            'process' => Process::ENROLLMENT_HISTORY,
            'active' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Menu::query()->where('process', Process::ENROLLMENT_HISTORY)->delete();
    }
}
