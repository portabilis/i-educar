<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

class ChangeProcessUpdateRegistrationStatusMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Menu::query()->where('link', '/atualiza-situacao-matriculas')->update(
            [
                'process' => Process::UPDATE_REGISTRATION_STATUS,
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
