<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

class CreateUpdateRegistrationStatusMenu extends Migration
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
            'title' => 'Atualização da situação de matrículas em lote',
            'link' => '/atualiza-situacao-matriculas',
            'process' => Process::UPDATE_REGISTRATION_STATUS,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Menu::query()->where('process', Process::UPDATE_REGISTRATION_STATUS)->delete();
    }
}
