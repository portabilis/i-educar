<?php

use App\Menu;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class RemoveAuditoriaMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $menus = Menu::query()
            ->where('process', 9998851)
            ->pluck('id')
            ->toArray();

        DB::table('pmieducar.menu_tipo_usuario')
            ->whereIn('menu_id', $menus)
            ->delete();

        Menu::query()->whereIn('id', $menus)->delete();
    }
}
