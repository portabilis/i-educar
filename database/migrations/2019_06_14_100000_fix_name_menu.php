<?php

use App\Menu;
use Illuminate\Database\Migrations\Migration;

class FixNameMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Menu::query()->where('process', 21246)->update([
            'title' => 'Cópia de rotas',
        ]);
    }
}
