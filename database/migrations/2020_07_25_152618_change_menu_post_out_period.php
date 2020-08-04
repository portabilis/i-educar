<?php

use App\Process;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeMenuPostOutPeriod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('menus')
            ->where('process', Process::POST_OUT_PERIOD)
            ->update([
                'title' => 'Permitir lançamento de notas/faltas fora do período definido por etapa',
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
                'title' => 'Permitir lançamento de notas/faltas fora do período de bloqueio por etapa',
            ]);
    }
}
