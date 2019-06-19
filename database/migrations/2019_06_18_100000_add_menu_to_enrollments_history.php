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
            'process' => Process::ENROLLMENT_HISTORY,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Menu::query()
            ->where('process', Process::ENROLLMENT_HISTORY)
            ->delete();
    }
}
