<?php

use App\Menu;
use App\Models\LegacyMenuUserType;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $menuId = Menu::query()
            ->where('process', 9998845)
            ->value('id');

        if ($menuId) {
            LegacyMenuUserType::query()
                ->where('menu_id', $menuId)
                ->delete();
        }

        Menu::query()
            ->where('process', 9998845)
            ->delete();
    }
};
