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

        DB::table('pmicontrolesis.menu')
            ->where('ref_cod_menu_submenu', 9998845)
            ->delete();

        DB::table('portal.menu_submenu')
            ->where('cod_menu_submenu', 9998845)
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('portal.menu_submenu')
            ->insert([
                'cod_menu_submenu' => 9998845,
                'ref_cod_menu_menu' => 70,
                'cod_sistema' => 2,
                'nm_submenu' => 'Exportação do educacenso - 2ª fase',
                'arquivo' => 'educar_exportacao_educacenso.php?fase2=1',
                'nivel' => 2,
            ]);

        DB::table('pmicontrolesis.menu')
            ->insert([
                'cod_menu' => 9998845,
                'ref_cod_menu_submenu' => 9998845,
                'ref_cod_menu_pai' => 999932,
                'tt_menu' => '2ª fase - Situação final',
                'ord_menu' => 1,
                'caminho' => 'educar_exportacao_educacenso.php?fase2=1',
                'alvo' => '_self',
                'suprime_menu' => 1,
                'ref_cod_tutormenu' => 22,
            ]);
    }
}
