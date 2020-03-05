<?php

use App\Process;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuSebExport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('public.menus')->insert(
            [
                'parent_id' => DB::table('public.menus')->where('old', Process::MENU_SCHOOL_TOOLS)
                    ->first()
                    ->id,
                'title' => 'Exportações',
                'description' => 'Exportações',
                'process' => Process::MENU_SCHOOL_TOOLS_EXPORTS,
                'active' => true,
            ]
        );

        DB::table('public.menus')->insert(
            [
                'parent_id' => DB::table('public.menus')->where('process', Process::MENU_SCHOOL_TOOLS_EXPORTS)
                    ->first()
                    ->id,
                'title' => 'Exportação para o SEB',
                'description' => 'Exportação para o SEB',
                'link' => '/exportacao-para-o-seb',
                'process' => Process::SEB_EXPORT,
                'active' => true,
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('public.menus')
            ->where('process', Process::SEB_EXPORT)
            ->delete();

        DB::table('public.menus')
            ->where('process', Process::MENU_SCHOOL_TOOLS_EXPORTS)
            ->delete();
    }
}
