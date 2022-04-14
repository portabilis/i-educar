<?php

use App\Process;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateUpdateSchoolclassReportCardMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('menus')
            ->where('process', Process::UPDATE_SCHOOL_CLASS_REPORT_CARD)
            ->update(['title' => 'Atualização de boletins em lote']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('menus')
            ->where('process', Process::UPDATE_SCHOOL_CLASS_REPORT_CARD)
            ->update(['title' => 'Alteração tipo de boletim de turmas']);
    }
}
