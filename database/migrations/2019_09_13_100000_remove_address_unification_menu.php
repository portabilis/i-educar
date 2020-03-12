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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Menu::query()->create([
            'parent_id' => Menu::query()->where('old', 68)->firstOrFail()->getKey(),
            'title' => 'Ferramentas',
            'order' => 1,
            'type' => 2,
            'old' => 999931,
            'parent_old' => 68,
        ]);

        Menu::query()->create([
            'parent_id' => Menu::query()->where('old', 999931)->firstOrFail()->getKey(),
            'title' => 'Unificação de bairros',
            'description' => 'Unificação de bairros',
            'link' => '/intranet/educar_unifica_bairro.php',
            'order' => 1,
            'type' => 3,
            'process' => 761,
            'old' => 761,
            'parent_old' => 999931,
        ]);

        Menu::query()->create([
            'parent_id' => Menu::query()->where('old', 999931)->firstOrFail()->getKey(),
            'title' => 'Unificação de logradouros',
            'description' => 'Unificação de logradouros',
            'link' => '/intranet/educar_unifica_logradouro.php',
            'order' => 2,
            'type' => 3,
            'process' => 762,
            'old' => 762,
            'parent_old' => 999931,
        ]);
    }
}
