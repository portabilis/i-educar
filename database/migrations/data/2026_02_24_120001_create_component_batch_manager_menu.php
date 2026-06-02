<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Menu::query()->updateOrCreate(['old' => Process::COMPONENT_BATCH_MANAGER], [
            'parent_id' => Menu::query()->where('old', Process::CONFIGURATIONS_TOOLS)->firstOrFail()->getKey(),
            'process' => Process::COMPONENT_BATCH_MANAGER,
            'title' => 'Gerenciamento em Lote de Componentes',
            'order' => 0,
            'parent_old' => Process::CONFIGURATIONS_TOOLS,
            'link' => '/gerenciamento-componentes',
        ]);
    }

    public function down(): void
    {
        Menu::query()->where('old', Process::COMPONENT_BATCH_MANAGER)->delete();
    }
};
