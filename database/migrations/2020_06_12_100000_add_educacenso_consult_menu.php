<?php

use App\Menu;
use Illuminate\Database\Migrations\Migration;

class AddEducacensoConsultMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $submenu = Menu::query()->updateOrCreate([
            'parent_id' => Menu::query()->where('process', 70)->firstOrFail()->getKey(),
            'title' => 'Consultas',
        ], [
            'order' => 3,
            'type' => 2,
        ]);

        Menu::query()->updateOrCreate([
            'process' => 847,
        ], [
            'parent_id' => $submenu->getKey(),
            'title' => 'Consulta 1ª fase - Matrícula inicial',
            'description' => 'Consulta dos dados que serão exportados para o Educacenso',
            'link' => '/educacenso/consulta',
            'order' => 1,
            'type' => 3,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Menu::query()
            ->where('process', 847)
            ->delete();

        Menu::query()
            ->where('parent_id', Menu::query()->where('process', 70)->firstOrFail()->getKey())
            ->where('title', 'Consultas')
            ->delete();
    }
}
