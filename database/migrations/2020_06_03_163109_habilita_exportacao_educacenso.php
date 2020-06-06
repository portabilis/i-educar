<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class HabilitaExportacaoEducacenso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('public.settings')
            ->where('key', 'legacy.educacenso.enable_export')
            ->update(['value' => '1']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('public.settings')
            ->where('key', 'legacy.educacenso.enable_export')
            ->update(['value' => '0']);
    }
}
