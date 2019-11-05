<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Process;

class AddReclassifyMenuToUserTypesWithRegistrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::insert('
            INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, menu_id, cadastra, visualiza, exclui)
            SELECT
                ref_cod_tipo_usuario,
                (
                    SELECT id
                    FROM menus
                    WHERE process = ?
                ) as menu_id,
                cadastra,
                visualiza,
                exclui
            FROM pmieducar.menu_tipo_usuario
            where menu_id = (
                SELECT id
                FROM menus
                WHERE process = ?
            )

        ', [
            Process::RECLASSIFY_REGISTRATION,
            Process::REGISTRATIONS,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('pmieducar.menu_tipo_usuario')
            ->whereRaw('menu_id = (
                SELECT id
                FROM menus
                WHERE process = ?
            )', [Process::RECLASSIFY_REGISTRATION])
            ->delete();
    }
}
