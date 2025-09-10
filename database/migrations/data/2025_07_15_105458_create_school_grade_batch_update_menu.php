<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Menu::query()->updateOrCreate(['old' => Process::SCHOOL_GRADE], [
            'parent_id' => Menu::query()->where('old', Process::CONFIGURATIONS_TOOLS)->firstOrFail()->getKey(),
            'process' => Process::SCHOOL_GRADE,
            'title' => 'Atualização de séries da escola em lote',
            'order' => 0,
            'parent_old' => Process::CONFIGURATIONS_TOOLS,
            'link' => '/atualizacao-em-lote-series-escola',
        ]);
    }

    public function down(): void
    {
        Menu::query()->where('old', Process::SCHOOL_GRADE)->delete();
    }
};
