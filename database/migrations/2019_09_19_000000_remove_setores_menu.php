<?php

use App\Menu;
use Illuminate\Database\Migrations\Migration;

class RemoveSetoresMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
            'parent_id' => Menu::query()->where('old', 999930),
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
