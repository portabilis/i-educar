<?php

use App\Menu;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RemoveDuplicatedLibraryMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $menus = Menu::query()
            ->where('process', 594)
            ->whereNull('link')
            ->pluck('id')
            ->toArray();

        DB::table('pmieducar.menu_tipo_usuario')
            ->whereIn('menu_id', $menus)
            ->delete();

        Menu::query()->whereIn('id', $menus)->update([
            'process' => null,
        ]);

        Menu::query()
            ->where('link', '/intranet/educar_categoria_lst.php')
            ->where('process', 598)
            ->update([
                'process' => 599,
                'description' => 'Categorias de obras'
            ]);
    }
}
