<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

class AddMenuExportacoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Menu::query()->create([
            'parent_id' => Menu::query()->where('old', Process::CONFIGURATIONS_TOOLS)->firstOrFail()->getKey(),
            'title' => 'Exportações de dados',
            'link' => '/exportacoes',
            'process' => Process::DATA_EXPORT,
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
            ->where('process', Process::DATA_EXPORT)
            ->delete();
    }
}
