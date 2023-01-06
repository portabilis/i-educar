<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertMenuManutencaoMatriculas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('public.menus')->insert(
            array(
                'id' => 3956,
                'parent_id' => 13,
                'title' => 'Manutenção de Matrículas',
                'description' => 'Manutenção de Matrículas',
                'link' => '/intranet/educar_manutencao_matricula.php',
                'icon' => NULL,
                'order' => 100,
                'type' => 1,
                'process' => 7797,
                'old' => NULL,
                'parent_old' => NULL,
                'active' => true,
                'created_at' => NULL,
                'updated_at' => NULL
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
