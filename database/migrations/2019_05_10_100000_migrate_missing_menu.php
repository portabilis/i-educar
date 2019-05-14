<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class MigrateMissingMenu extends Migration
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
                insert into menus("parent_id", "title", "description", "link", "icon", "order", "type", "process", "old", "parent_old", "active")
                select 
                    (select id from menus where process = 55 limit 1) as parent_id,
                    nm_submenu as title,
                    null as description,
                    null as link,
                    null as icon,
                    9999 as "order",
                    2 as "type",
                    cod_menu_submenu as process,
                    cod_menu_submenu as "old",
                    55 as parent_old,
                    true as active
                from portal.menu_submenu 
                where true 
                and cod_sistema <> 2 
                and ref_cod_menu_menu = 55;
            '
        );
    }
}
