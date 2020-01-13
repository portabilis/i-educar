<?php

use App\Menu;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RemoveSetoresMenu extends Migration
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
                    select id from menus where old in (760)
                );
            '
        );

        Menu::query()->where('process', 760)->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Menu::query()->create([
            'parent_id' => Menu::query()->where('old', 999930)->firstOrFail()->getKey(),
            'title' => 'Setores',
            'description' => 'Setores',
            'link' => '/intranet/public_setor_lst.php',
            'order' => 5,
            'type' => 3,
            'process' => 760,
            'old' => 760,
            'parent_old' => 999930,
        ]);
    }
}
