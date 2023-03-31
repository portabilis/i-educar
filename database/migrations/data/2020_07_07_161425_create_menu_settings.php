<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

class CreateMenuSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Menu::query()->create([
            'parent_id' => Menu::query()->where('process', Process::CONFIG)->value('parent_id'),
            'title' => 'Configurações de sistema',
            'link' => '/configuracoes/configuracoes-de-sistema',
            'process' => Process::SETTINGS,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Menu::query()->where('process', Process::SETTINGS)->delete();
    }
}
