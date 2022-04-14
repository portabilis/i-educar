<?php

use App\Menu;
use App\Process;
use Illuminate\Database\Migrations\Migration;

class CreateUpdateSchoolclassReportCardMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Menu::query()->create([
            'parent_id' => Menu::query()->where('old', Process::CONFIGURATIONS_TOOLS)->firstOrFail()->getKey(),
            'title' => 'Alteração tipo de boletim de turmas',
            'link' => '/alterar-tipo-boletim-turmas',
            'process' => Process::UPDATE_SCHOOL_CLASS_REPORT_CARD,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Menu::query()->where('process', Process::UPDATE_SCHOOL_CLASS_REPORT_CARD)->delete();
    }
}
