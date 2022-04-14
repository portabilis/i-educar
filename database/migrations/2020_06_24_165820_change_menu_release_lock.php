<?php

use App\Process;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeMenuReleaseLock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('menus')
            ->where('process', Process::RELEASE_PERIOD)
            ->update([
                'title' => 'Período de lançamento de notas e faltas por etapa',
                'description' => 'Período de lançamento de notas e faltas por etapa',
                'link' => '/periodo-lancamento',
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('menus')
            ->where('process', Process::RELEASE_PERIOD)
            ->update([
                'title' => 'Bloqueio de lançamento de notas e faltas',
                'description' => 'Bloqueio de lançamento de notas e faltas',
                'link' => '/intranet/educar_bloqueio_lancamento_faltas_notas_lst.php',
            ]);
    }
}
