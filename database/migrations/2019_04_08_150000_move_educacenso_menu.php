<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class MoveEducacensoMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('portal.menu_submenu')->whereIn('cod_menu_submenu', [846, 9998845])->update([
            'nivel' => 3,
        ]);
    }
}
