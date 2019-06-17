<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdicionaMenuAtaResultadoFinal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("INSERT INTO portal.menu_submenu values (9998911, 55, 2, 'Ata Resultado final', 'module/Reports/AtaResultadoFinal',null,3);");
        DB::statement("INSERT INTO pmicontrolesis.menu VALUES(9998911,9998911,999925,'Ata Resultado final',0,'module/Reports/AtaResultadoFinal','_self',1,15,192);");
        DB::statement("INSERT INTO pmieducar.menu_tipo_usuario VALUES(1,9998911,1,1,1);");

        DB::statement("INSERT INTO menus (parent_id, title, description, link, icon, \"order\", type, process, old, parent_old, active) VALUES (77,'Ata de resultado final', 'Ata de resultado final', '/module/Reports/AtaResultadoFinal','',0,4,9998911,9998911,999925,true);");
    }

    public function down()
    {
        DB::statement('delete from menus where parent_old = 999925 and old = 9998911 and process = 9998911;');
        DB::statement('delete from pmieducar.menu_tipo_usuario where ref_cod_tipo_usuario = 1 and ref_cod_menu_submenu = 9998911;');
        DB::statement('delete from pmicontrolesis.menu where cod_menu = 9998911;');
        DB::statement('delete from portal.menu_submenu where cod_menu_submenu = 9998911;');

    }
}
