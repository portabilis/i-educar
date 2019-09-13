<?php

use App\Menu;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class RemoveAddressesMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            '
                DELETE FROM pmieducar.menu_tipo_usuario WHERE menu_id in (
                    select id from menus where old in (756, 757, 758, 760)
                );
            '
        );

        Menu::query()->whereIn('old', [756, 757, 758, 760])->delete();
    }
}
