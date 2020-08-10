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
            'parent_id' => Menu::query()->where('old', Process::CONFIG)->firstOrFail()->getKey(),
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
