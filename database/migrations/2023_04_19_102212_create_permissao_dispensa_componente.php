<?php

use App\Menu;
use App\Models\LegacyMenuUserType;
use App\Models\LegacyUserType;
use App\Process;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    public function up(): void
    {
        $parent = Menu::query()->where('old', Process::MENU_SCHOOL)->first();

        if ($parent) {
            $menu = Menu::query()->updateOrCreate([
                'old' => 628,
            ], [
                'parent_id' => $parent->getKey(),
                'title' => 'Dispensar componente curricular na matrícula',
                'order' => 9999,
                'type' => 2,
                'parent_old' => Process::MENU_SCHOOL,
                'process' => 628,
                'ativo' => true,
            ]);
            if ($menu) {
                $userTypes = LegacyUserType::all();
                $userTypes->each(static function (LegacyUserType $userType) use ($menu) {
                    $userType->menus()->attach($menu, [
                        'visualiza' => 1,
                        'cadastra' => 1,
                        'exclui' => 1,
                    ]);
                });
            }
        }
    }

    public function down(): void
    {
        $menu = Menu::query()
            ->where('process', 628)
            ->first();

        if ($menu) {
            LegacyMenuUserType::query()
                ->where('menu_id', $menu->getKey())
                ->delete();
            $menu->delete();
        }
    }
};
