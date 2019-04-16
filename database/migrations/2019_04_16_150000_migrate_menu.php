<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class MigrateMenu extends Migration
{
    /**
     * @return void
     */
    private function migrateSidebarMenu()
    {
        DB::unprepared(
            '
                insert into menus("parent_id", "title", "description", "link", "icon", "order", "type", "process", "old", "parent_old", "active")
                select 
                    null as parent_id,
                    nm_menu as title,
                    null as description,
                    caminho as link,
                    icon_class as icon,
                    ord_menu as "order",
                    1 as type,
                    cod_menu_menu as process,
                    cod_menu_menu as "old",
                    null as "parent_old",
                    true as active
                from portal.menu_menu
                where ativo
                order by ord_menu;
            '
        );
    }

    /**
     * @return void
     */
    private function migrateTopMenu()
    {
        DB::unprepared(
            '
                insert into menus("parent_id", "title", "description", "link", "icon", "order", "type", "process", "old", "parent_old", "active")
                select 
                    (select id from menus where parent_old = m.ref_cod_menu_pai) as parent_id,
                    m.tt_menu as title,
                    null as description,
                    null as link,
                    null as icon,
                    m.ord_menu as "order",
                    2 as type,
                    null as process,
                    m.cod_menu as "old",
                    (
                        case 
                            when m.ref_cod_tutormenu = 15 then 55
                            when m.ref_cod_tutormenu = 16 then 57
                            when m.ref_cod_tutormenu = 17 then 69
                            when m.ref_cod_tutormenu = 18 then 25
                            when m.ref_cod_tutormenu = 19 then 71
                            when m.ref_cod_tutormenu = 20 then 7
                            when m.ref_cod_tutormenu = 21 then 68
                            when m.ref_cod_tutormenu = 22 then 70
                        end
                    ) as parent_old,
                    true as active
                from pmicontrolesis.menu m 
                where true 
                and m.ref_cod_menu_pai is null
                and m.ref_cod_tutormenu in (15, 16, 17, 18, 19, 20, 21, 22)
                and m.cod_menu in (
                    select ref_cod_menu_pai from pmicontrolesis.menu
                )
                order by parent_old, m.ord_menu, m.tt_menu;
            '
        );
    }

    /**
     * @return void
     */
    private function migrateSubmenuLevel1()
    {
        DB::unprepared(
            '
                insert into menus("parent_id", "title", "description", "link", "icon", "order", "type", "process", "old", "parent_old", "active")
                select 
                    (select id from menus where parent_old = m.ref_cod_menu_pai limit 1) as parent_id,
                    m.tt_menu as title,
                    ms.nm_submenu as description,
                    caminho as link,
                    null as icon,
                    m.ord_menu as "order",
                    3 as type,
                    ms.cod_menu_submenu as process,
                    m.cod_menu as "old",
                    m.ref_cod_menu_pai as parent_old,
                    true as active
                from pmicontrolesis.menu m 
                left join portal.menu_submenu ms 
                on ms.cod_menu_submenu = m.ref_cod_menu_submenu
                where true 
                and m.ref_cod_tutormenu in (15, 16, 17, 18, 19, 20, 21, 22)
                and m.ref_cod_menu_pai in (
                    select "old" from menus where "type" = 2
                )
                and (
                    m.caminho is not null
                    or
                    m.cod_menu in (
                        select ref_cod_menu_pai from pmicontrolesis.menu
                    )	
                )
                order by parent_old, m.ord_menu, m.tt_menu;
            '
        );
    }

    /**
     * @return void
     */
    private function migrateSubmenuLevel2()
    {
        DB::unprepared(
            '
                insert into menus("parent_id", "title", "description", "link", "icon", "order", "type", "process", "old", "parent_old", "active")
                select 
                    (select id from menus where parent_old = m.ref_cod_menu_pai) as parent_id,
                    m.tt_menu as title,
                    ms.nm_submenu as description,
                    caminho as link,
                    null as icon,
                    m.ord_menu as "order",
                    4 as type,
                    ms.cod_menu_submenu as process,
                    m.cod_menu as "old",
                    m.ref_cod_menu_pai as parent_old,
                    true as active
                from pmicontrolesis.menu m 
                left join portal.menu_submenu ms 
                on ms.cod_menu_submenu = m.ref_cod_menu_submenu
                where true 
                and m.ref_cod_tutormenu in (15, 16, 17, 18, 19, 20, 21, 22)
                and m.ref_cod_menu_pai in (
                    select "old" from menus where "type" = 3
                )
                and (
                    m.caminho is not null
                    or
                    m.cod_menu in (
                        select ref_cod_menu_pai from pmicontrolesis.menu
                    )	
                )
                order by parent_old, m.ord_menu, m.tt_menu;
            '
        );
    }

    /**
     * @return void
     */
    private function migrateSubmenuLevel3()
    {
        DB::unprepared(
            '
                insert into menus("parent_id", "title", "description", "link", "icon", "order", "type", "process", "old", "parent_old", "active")
                select 
                    (select id from menus where parent_old = m.ref_cod_menu_pai limit 1) as parent_id,
                    m.tt_menu as title,
                    ms.nm_submenu as description,
                    caminho as link,
                    null as icon,
                    m.ord_menu as "order",
                    5 as type,
                    ms.cod_menu_submenu as process,
                    m.cod_menu as "old",
                    m.ref_cod_menu_pai as parent_old,
                    true as active
                from pmicontrolesis.menu m 
                left join portal.menu_submenu ms 
                on ms.cod_menu_submenu = m.ref_cod_menu_submenu
                where true 
                and m.ref_cod_tutormenu in (15, 16, 17, 18, 19, 20, 21, 22)
                and m.ref_cod_menu_pai in (
                    select "old" from menus where "type" = 4
                )
                and (
                    m.caminho is not null
                    or
                    m.cod_menu in (
                        select ref_cod_menu_pai from pmicontrolesis.menu
                    )	
                )
                order by parent_old, m.ord_menu, m.tt_menu;
            '
        );
    }

    /**
     * @return void
     */
    private function migrateSubmenuLevel4()
    {
        DB::unprepared(
            '
                insert into menus("parent_id", "title", "description", "link", "icon", "order", "type", "process", "old", "parent_old", "active")
                select 
                    (select id from menus where parent_old = m.ref_cod_menu_pai limit 1) as parent_id,
                    m.tt_menu as title,
                    ms.nm_submenu as description,
                    caminho as link,
                    null as icon,
                    m.ord_menu as "order",
                    6 as type,
                    ms.cod_menu_submenu as process,
                    m.cod_menu as "old",
                    m.ref_cod_menu_pai as parent_old,
                    true as active
                from pmicontrolesis.menu m 
                left join portal.menu_submenu ms 
                on ms.cod_menu_submenu = m.ref_cod_menu_submenu
                where true 
                and m.ref_cod_tutormenu in (15, 16, 17, 18, 19, 20, 21, 22)
                and m.ref_cod_menu_pai in (
                    select "old" from menus where "type" = 5
                )
                and (
                    m.caminho is not null
                    or
                    m.cod_menu in (
                        select ref_cod_menu_pai from pmicontrolesis.menu
                    )	
                )
                order by parent_old, m.ord_menu, m.tt_menu;
            '
        );
    }

    /**
     * @return void
     */
    private function migrateRelations()
    {
        DB::unprepared(
            '   
                update menus m set parent_id = (select id from menus where old = m.parent_old) where parent_old is not null;
            '
        );
    }

    /**
     * @return void
     */
    public function up()
    {
        $this->migrateSidebarMenu();
        $this->migrateTopMenu();
        $this->migrateSubmenuLevel1();
        $this->migrateSubmenuLevel2();
        $this->migrateSubmenuLevel3();
        $this->migrateSubmenuLevel4();
        $this->migrateRelations();
    }
}
