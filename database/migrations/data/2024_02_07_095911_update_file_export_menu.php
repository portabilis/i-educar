<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Menu::query()
            ->where('process', Process::DOCUMENT_EXPORT)
            ->update([
                'title' => 'Exportador de Documentos',
                'parent_id' => Menu::query()->where('old', Process::MENU_SCHOOL_TOOLS)->valueOrFail('id'),
                'parent_old' => Process::MENU_SCHOOL_TOOLS,
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
            ->where('process', Process::DOCUMENT_EXPORT)
            ->update([
                'title' => 'Exportações de documentos',
                'parent_id' => Menu::query()->where('old', Process::CONFIGURATIONS_TOOLS)->valueOrFail('id'),
                'parent_old' => Process::CONFIGURATIONS_TOOLS,
            ]);
    }
};
