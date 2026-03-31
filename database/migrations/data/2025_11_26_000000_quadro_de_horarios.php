<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Menu::query()->create([
            'title' => 'Quadro de horários',
            'link' => '/new/quadro-de-horarios',
            'process' => Process::TIMETABLE,
            'old' => Process::TIMETABLE,

            'parent_id' => Menu::query()->where('old', Process::MENU_SCHOOL_TOOLS)->valueOrFail('id'),
            'parent_old' => Process::MENU_SCHOOL_TOOLS,
        ]);
    }

    public function down(): void
    {
        Menu::query()
            ->where('process', Process::TIMETABLE)
            ->delete();
    }
};
