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
    }
}
