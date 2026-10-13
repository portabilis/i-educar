<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Menu::query()
            ->where('process', 641)
            ->get()
            ->each(function (Menu $menu) {
                DB::table('pmieducar.menu_tipo_usuario')
                    ->where('menu_id', $menu->getKey())
                    ->delete();

                $menu->delete();
            });

        $menu = Menu::query()->where('process', Process::TIMETABLE)->first();
        $parentId = Menu::query()->where('old', 999911)->value('id');

        if (empty($menu) || empty($parentId)) {
            return;
        }

        $menu->update([
            'parent_id' => $parentId,
            'parent_old' => 999911,
            'order' => 3,
        ]);
    }

    public function down(): void
    {
        $menu = Menu::query()->where('process', Process::TIMETABLE)->first();
        $parentId = Menu::query()->where('old', Process::MENU_SCHOOL_TOOLS)->value('id');

        if (empty($menu) || empty($parentId)) {
            return;
        }

        $menu->update([
            'parent_id' => $parentId,
            'parent_old' => Process::MENU_SCHOOL_TOOLS,
            'order' => 99,
        ]);
    }
};
