<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

class CreateUpdateRegistrationDateMenu extends Migration
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
            'title' => 'Atualização da data de entrada e enturmação em lote',
            'link' => '/atualiza-data-entrada',
            'process' => Process::UPDATE_REGISTRATION_DATE,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Menu::query()->where('process', Process::UPDATE_REGISTRATION_DATE)->delete();
    }
}
