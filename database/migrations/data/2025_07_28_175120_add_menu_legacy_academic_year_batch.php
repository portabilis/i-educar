<?php

declare(strict_types=1);

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Menu::query()->updateOrCreate(['old' => Process::ACADEMIC_YEAR_IMPORT], [
            'parent_id' => Menu::query()->where('old', Process::CONFIGURATIONS_TOOLS)->firstOrFail()->getKey(),
            'process' => Process::ACADEMIC_YEAR_IMPORT,
            'title' => 'Ano Letivo em Lote',
            'description' => 'Importação de anos letivos em lote',
            'order' => 0,
            'parent_old' => Process::CONFIGURATIONS_TOOLS,
            'link' => '/ano-letivo-em-lote',
        ]);
    }

    public function down(): void
    {
        Menu::query()->where('process', Process::ACADEMIC_YEAR_IMPORT)->delete();
    }
};
