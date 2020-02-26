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
                    select id from menus where old in (756, 757, 758)
                );
            '
        );

        Menu::query()->whereIn('old', [756, 757, 758])->delete();
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
            'title' => 'CEP',
            'description' => 'CEP',
            'link' => '/intranet/urbano_cep_logradouro_lst.php',
            'order' => 8,
            'type' => 3,
            'process' => 758,
            'old' => 758,
            'parent_old' => 999930,
        ]);

        Menu::query()->create([
            'parent_id' => Menu::query()->where('old', 999930)->firstOrFail()->getKey(),
            'title' => 'Bairros',
            'description' => 'Bairros',
            'link' => '/intranet/public_bairro_lst.php',
            'order' => 6,
            'type' => 3,
            'process' => 756,
            'old' => 756,
            'parent_old' => 999930,
        ]);

        Menu::query()->create([
            'parent_id' => Menu::query()->where('old', 999930)->firstOrFail()->getKey(),
            'title' => 'Setores',
            'description' => 'Setores',
            'link' => '/intranet/public_logradouro_lst.php',
            'order' => 7,
            'type' => 3,
            'process' => 757,
            'old' => 757,
            'parent_old' => 999930,
        ]);
    }
}
