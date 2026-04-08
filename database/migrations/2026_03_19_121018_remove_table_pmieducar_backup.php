<?php

use App\Menu;
use App\Models\LegacyMenuUserType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $menuId = Menu::query()
            ->where('process', 9998858)
            ->value('id');

        LegacyMenuUserType::query()
            ->where('menu_id', $menuId)
            ->delete();

        Menu::query()
            ->where('process', 9998858)
            ->delete();

        Schema::dropIfExists('pmieducar.backup');
    }
};
