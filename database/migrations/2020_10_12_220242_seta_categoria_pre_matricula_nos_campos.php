<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetaCategoriaPreMatriculaNosCampos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            UPDATE public.settings
                SET setting_category_id = 11
            WHERE key IN (
                'prematricula.active',
                'prematricula.city',
                'prematricula.state',
                'prematricula.map.lat',
                'prematricula.map.lng',
                'prematricula.map.zoom'
            )
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
            UPDATE public.settings
                SET setting_category_id = 1
            WHERE key IN (
                'prematricula.active',
                'prematricula.city',
                'prematricula.state',
                'prematricula.map.lat',
                'prematricula.map.lng',
                'prematricula.map.zoom'
            )
        ");
    }
}
