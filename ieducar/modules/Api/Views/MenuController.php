<?php

use App\Menu;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';

class MenuController extends ApiCoreController
{

    private function getCurrentUser()
    {
        $this->pessoa_logada = Session::get('id_pessoa');

        return $this->pessoa_logada;
    }

    protected function sqlsForNumericSearch()
    {
        $usuario = $this->getCurrentUser();
        $sqls[] = "
            select 
                m.link as id, 
                coalesce(m.description, m.title) as name
            from public.menus m 
            inner join pmieducar.menu_tipo_usuario mst
            on mst.ref_cod_menu_submenu = m.process
            inner join pmieducar.usuario u 
            on u.ref_cod_tipo_usuario = mst.ref_cod_tipo_usuario
            where true 
            and u.cod_usuario = '{$usuario}'
            and mst.visualiza = 1
            and m.link is not null
            and m.process = $1
            order by m.title
            limit 15;
        ";

        return $sqls;
    }

    protected function sqlsForStringSearch()
    {
        $usuario = $this->getCurrentUser();

        $sqls[] = "
            select 
                m.link as id, 
                coalesce(m.description, m.title) as name
            from public.menus m 
            inner join pmieducar.menu_tipo_usuario mst
            on mst.ref_cod_menu_submenu = m.process
            inner join pmieducar.usuario u 
            on u.ref_cod_tipo_usuario = mst.ref_cod_tipo_usuario
            where true 
            and u.cod_usuario = '{$usuario}'
            and mst.visualiza = 1
            and m.link is not null
            and (
                m.title ilike '%'|| $1 ||'%'
                or m.description ilike '%'|| $1 ||'%'
            )
            order by m.title
            limit 15;
        ";

        return $sqls;
    }

    protected function formatResourceValue($resource)
    {
        return $this->toUtf8($resource['name']);
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'menu-search')) {
            $this->appendResponse($this->search());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
