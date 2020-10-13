<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AjustaTipoDeCampoQueAtivaPrematricula extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('public.settings')
            ->where('key', 'prematricula.active')
            ->update(['type' => 'boolean']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('public.settings')
            ->where('key', 'prematricula.active')
            ->update(['type' => 'string']);
    }
}
