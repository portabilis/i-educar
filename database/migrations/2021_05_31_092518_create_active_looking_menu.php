<?php

use App\Menu;
use Illuminate\Database\Migrations\Migration;

class CreateActiveLookingMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $parent_id = Menu::query()->where('old', 55)->firstOrFail()->id;

        Menu::query()->create([
            'title' => 'Busca ativa',
            'parent_id' => $parent_id,
            'process' => 9998921,
        ]);
    }

    public function down()
    {
        Menu::query()
            ->where('process', 9998921)
            ->delete();
    }
}
