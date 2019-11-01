<?php

use App\Menu;
use App\Process;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuReclassifyRegistration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Menu::query()->create([
            'parent_id' => Menu::query()->where('old', Process::MENU_SCHOOL)->firstOrFail()->getKey(),
            'title' => 'Reclassificar matrícula',
            'process' => Process::RECLASSIFY_REGISTRATION,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Menu::query()->where('process', Process::RECLASSIFY_REGISTRATION)->delete();
    }
}
