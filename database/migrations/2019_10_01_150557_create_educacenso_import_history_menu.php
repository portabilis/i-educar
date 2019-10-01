<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

class CreateEducacensoImportHistoryMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Menu::query()->create([
            'parent_id' => Menu::query()->where('old', Process::EDUCACENSO_IMPORTACOES)->firstOrFail()->getKey(),
            'title' => 'Histórico de importações',
            'link' => '/educacenso/importacao/historico',
            'process' => Process::EDUCACENSO_IMPORT_HISTORY,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Menu::query()->where('process', Process::EDUCACENSO_IMPORT_HISTORY)->delete();
    }
}
