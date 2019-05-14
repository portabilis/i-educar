<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveMenu2FaseExportacaoEducacenso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('pmieducar.menu_tipo_usuario')
            ->where('ref_cod_menu_submenu', 9998845)
            ->delete();
    }
}
