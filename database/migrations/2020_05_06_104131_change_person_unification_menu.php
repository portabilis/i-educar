<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangePersonUnificationMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('menus')
            ->where('process', 9998878)
            ->update(['link' => '/unificacao-pessoa']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('menus')
            ->where('process', 9998878)
            ->update(['link' => '/intranet/educar_unifica_pessoa.php']);
    }
}
