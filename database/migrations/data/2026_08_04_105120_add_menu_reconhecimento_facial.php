<?php

declare(strict_types=1);

use App\Menu;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Menu::query()->updateOrCreate(['old' => 5781], [
            'parent_id' => Menu::query()->where('old', 60)->firstOrFail()->getKey(),
            'process' => 5781,
            'title' => 'Reconhecimento facial',
            'description' => 'Menu para acessar a tela de reconhecimento facial',
            'order' => 0,
            'parent_old' => 21205,
            'type' => 2,
        ]);
    }

    public function down(): void
    {
        Menu::query()->where('process', 5781)->delete();
    }
};
