<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Menu::query()->updateOrCreate(['old' => Process::ANNOUNCEMENT], [
            'parent_id' => Menu::query()->where('old', Process::CONFIGURATIONS_TOOLS)->firstOrFail()->getKey(),
            'process' => Process::ANNOUNCEMENT,
            'title' => 'Publicação de avisos',
            'order' => 0,
            'parent_old' => Process::CONFIGURATIONS_TOOLS,
            'link' => '/avisos/publicacao',
        ]);
    }

    public function down(): void
    {
        Menu::query()->where('old', Process::ANNOUNCEMENT)->delete();
    }
};
