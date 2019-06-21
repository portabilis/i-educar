<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class MigrateMenuColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            '
                update pmieducar.menu_tipo_usuario
                set menu_id = tmp.id
                from (
                    select menus.id, mtu.ref_cod_menu_submenu
                    from pmieducar.menu_tipo_usuario mtu
                    inner join menus 
                    on menus.process = mtu.ref_cod_menu_submenu
                ) as tmp
                where menu_tipo_usuario.ref_cod_menu_submenu = tmp.ref_cod_menu_submenu;
            '
        );

        DB::unprepared(
            '
                delete from pmieducar.menu_tipo_usuario
                where menu_id is null;
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
        DB::unprepared(
            '
                update pmieducar.menu_tipo_usuario
                set ref_cod_menu_submenu = tmp.process
                from (
                    select menus.id, menus.process
                    from pmieducar.menu_tipo_usuario mtu
                    inner join menus 
                    on menus.id = mtu.menu_id
                ) as tmp
                where menu_tipo_usuario.menu_id = tmp.id;
            '
        );

        DB::unprepared(
            '
                delete from pmieducar.menu_tipo_usuario
                where ref_cod_menu_submenu is null;
            '
        );
    }
}
