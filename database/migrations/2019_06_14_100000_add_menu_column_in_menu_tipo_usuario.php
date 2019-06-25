    <?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMenuColumnInMenuTipoUsuario extends Migration
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
                ALTER TABLE pmieducar.menu_tipo_usuario
                DROP CONSTRAINT IF EXISTS menu_tipo_usuario_ref_cod_menu_submenu_fkey;
            '
        );

        Schema::table('pmieducar.menu_tipo_usuario', function (Blueprint $table) {
            $table->integer('menu_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.menu_tipo_usuario', function (Blueprint $table) {
            $table->dropColumn('menu_id');
        });

        DB::unprepared(
            '
                ALTER TABLE pmieducar.menu_tipo_usuario 
                ALTER COLUMN ref_cod_menu_submenu 
                SET NOT NULL;
            '
        );

        DB::unprepared(
            '
                ALTER TABLE pmieducar.menu_tipo_usuario
                ADD CONSTRAINT menu_tipo_usuario_ref_cod_menu_submenu_fkey
                FOREIGN KEY (ref_cod_menu_submenu)
                REFERENCES portal.menu_submenu(cod_menu_submenu)
                ON UPDATE RESTRICT ON DELETE RESTRICT;
            '
        );
    }
}
