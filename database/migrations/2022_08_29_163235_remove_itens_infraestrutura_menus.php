<?php

use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            'DELETE FROM pmieducar.menu_tipo_usuario
                WHERE menu_id IN (SELECT id FROM public.menus WHERE process in (567, 572, 574)
                OR UPPER(title) = \'INFRAESTRUTURA\');
            '
        );
        DB::statement(
            'DELETE FROM public.menus
                WHERE process in (567, 572, 574)
                OR UPPER(title) = \'INFRAESTRUTURA\';
            '
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
