<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

class AddMenuToAllowStudentReturnToQueue extends Migration
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
            'title' => 'Permite voltar para "Em Espera" candidaturas do Reserva de Vagas já atendidas',
            'process' => Process::BACK_TO_QUEUE,
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
            ->where('process', Process::BACK_TO_QUEUE)
            ->delete();
    }
}
