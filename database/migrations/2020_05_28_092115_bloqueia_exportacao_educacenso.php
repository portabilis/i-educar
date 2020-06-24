<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class BloqueiaExportacaoEducacenso extends Migration
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
            ->update(['value' => '0']);
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
            ->update(['value' => '1']);
    }
}
