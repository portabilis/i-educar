<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Menu::query()->create([
            'parent_id' => Menu::query()->where('process', Process::MENU_SCHOOL)->firstOrFail()->getKey(),
            'title' => 'Notificar busca ativa',
            'process' => Process::NOTIFY_ACTIVE_LOOKING,
        ]);
    }

    public function down(): void
    {
        Menu::query()
            ->where('process', Process::NOTIFY_ACTIVE_LOOKING)
            ->delete();
    }
};
