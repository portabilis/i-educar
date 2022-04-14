<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

class CriaPermissaoNotificarTransferencia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Menu::query()->create([
            'parent_id' => Menu::query()->where('process', Process::MENU_SCHOOL)->firstOrFail()->getKey(),
            'title' => 'Notificar transferências',
            'process' => Process::NOTIFY_TRANSFER,
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
            ->where('process', Process::NOTIFY_TRANSFER)
            ->delete();
    }
}
