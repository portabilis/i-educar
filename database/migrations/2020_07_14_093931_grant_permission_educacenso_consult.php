<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class GrantPermissionEducacensoConsult extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            'INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, cadastra, visualiza, exclui, menu_id)
                        SELECT ref_cod_tipo_usuario,
                        1,
                        1,
                        1,
                        (SELECT id FROM public.menus WHERE process = 847 LIMIT 1)
                   FROM pmieducar.menu_tipo_usuario
                   WHERE menu_id = (SELECT id FROM public.menus WHERE process = 846 LIMIT 1)
                   AND NOT EXISTS(
                     SELECT 1 FROM pmieducar.menu_tipo_usuario mtu
                     WHERE mtu.ref_cod_tipo_usuario = menu_tipo_usuario.ref_cod_tipo_usuario
                       AND mtu.menu_id = (SELECT id FROM public.menus WHERE process = 847 LIMIT 1))');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DELETE FROM pmieducar.menu_tipo_usuario WHERE menu_id = (SELECT id FROM public.menus WHERE process = 847)');
    }
}
