<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnFromMenuTipoUsuario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.menu_tipo_usuario', function (Blueprint $table) {
            $table->dropColumn('ref_cod_menu_submenu');
        });

        DB::unprepared(
            '
                ALTER TABLE pmieducar.menu_tipo_usuario 
                ALTER COLUMN menu_id 
                SET NOT NULL;
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
        Schema::table('pmieducar.menu_tipo_usuario', function (Blueprint $table) {
            $table->integer('ref_cod_menu_submenu')->nullable();
        });

        DB::unprepared(
            '
                ALTER TABLE pmieducar.menu_tipo_usuario 
                ALTER COLUMN menu_id
                SET DEFAULT NULL;
            '
        );
    }
}
