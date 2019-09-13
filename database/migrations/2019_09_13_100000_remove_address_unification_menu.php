<?php

use App\Menu;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RemoveAddressUnificationMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DELETE FROM pmieducar.menu_tipo_usuario WHERE menu_id in (select id from menus where old in (761, 762, 999931))');

        Menu::query()->whereIn('old', [761, 762])->delete();
        Menu::query()->where('old', 999931)->delete();
    }
}
