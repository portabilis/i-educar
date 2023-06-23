<?php

use App\Menu;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    public function up()
    {
        Menu::where('process', '631')->update([
            'title' => 'Tipos de deficiência e transtornos',
            'description' => 'Tipos de deficiência e transtornos da pessoa',
        ]);
    }

    public function down()
    {
        Menu::where('process', '631')->update([
            'title' => 'Tipos de deficiência',
            'description' => 'Tipos de deficiência da pessoa',
        ]);
    }
};
