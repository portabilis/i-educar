<?php

declare(strict_types=1);

use App\Menu;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Menu::query()->updateOrCreate(['old' => 5841], [
            'parent_id' => Menu::query()->where('old', 21161)->firstOrFail()->getKey(),
            'process' => 5841,
            'title' => 'Regional',
            'description' => 'Cadastro de Regionais',
            'order' => 0,
            'parent_old' => 21161,
            'link' => '/intranet/educar_regional_lst.php',
        ]);
    }

    public function down(): void
    {
        Menu::query()->where('process', 5841)->delete();
    }
};
