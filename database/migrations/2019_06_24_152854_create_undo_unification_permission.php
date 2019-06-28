<?php

use App\Menu;
use App\Process;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUndoUnificationPermission extends Migration
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
            'title' => 'Desfazer unificação de alunos',
            'process' => Process::UNDO_STUDENT_UNIFICATION,
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
            ->where('process', Process::UNDO_STUDENT_UNIFICATION)
            ->delete();
    }
}
