<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Menu::query()->updateOrCreate(['old' => Process::FINAL_STATUS_IMPORT], [
            'parent_id' => Menu::query()->where('old', Process::CONFIGURATIONS_TOOLS)->firstOrFail()->getKey(),
            'process' => Process::FINAL_STATUS_IMPORT,
            'title' => 'Importação de Situação Final',
            'description' => 'Importação de situação final das matrículas via arquivo CSV',
            'order' => 0,
            'parent_old' => Process::CONFIGURATIONS_TOOLS,
            'link' => '/importacao-situacao-final',
        ]);
    }

    public function down(): void
    {
        Menu::query()
            ->where('process', Process::FINAL_STATUS_IMPORT)
            ->delete();
    }
};
