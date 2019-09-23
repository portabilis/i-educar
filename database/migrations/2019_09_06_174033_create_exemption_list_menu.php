<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

class CreateExemptionListMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Menu::query()->create([
            'parent_id' => Menu::query()->where('old', Process::CONSULTAS)->firstOrFail()->getKey(),
            'title' => 'Consulta de dispensas',
            'link' => '/consulta-dispensas',
            'process' => Process::EXEMPTION_LIST,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Menu::query()->where('process', Process::EXEMPTION_LIST)->delete();
    }
}
