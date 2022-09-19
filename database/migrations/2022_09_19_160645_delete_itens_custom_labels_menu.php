<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::statement(
            'DELETE FROM pmieducar.menu_tipo_usuario
                WHERE menu_id IN (SELECT id FROM public.menus WHERE process in (9998869));'
        );
        DB::statement(
            'DELETE FROM public.menus
                WHERE process in (9998869);'
        );
    }
};
