<?php

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
        $sqls[] =
              "select arquivo as id,
                      nm_submenu as name
                 from portal.menu_submenu as ms
                left  join pmicontrolesis.menu as m on(m.ref_cod_menu_submenu = ms.cod_menu_submenu)
                inner join pmieducar.menu_tipo_usuario as mtu on(ms.cod_menu_submenu = mtu.ref_cod_menu_submenu)
                inner join pmieducar.usuario as u on (u.ref_cod_tipo_usuario = mtu.ref_cod_tipo_usuario)
                where ms.cod_menu_submenu = $1 AND
                      arquivo is not null AND
                      trim(arquivo) <> '' AND
                      mtu.visualiza = 1 AND
                      u.cod_usuario = '{$usuario}'
                      limit 15";

        return $sqls;
    }

    protected function sqlsForStringSearch()
    {
        $usuario = $this->getCurrentUser();
        $sqls[] =
            "select arquivo as id,
                    nm_submenu as name
               from portal.menu_submenu as ms
              left  join pmicontrolesis.menu as m on(m.ref_cod_menu_submenu = ms.cod_menu_submenu)
              inner join pmieducar.menu_tipo_usuario as mtu on(ms.cod_menu_submenu = mtu.ref_cod_menu_submenu)
              inner join pmieducar.usuario as u on (u.ref_cod_tipo_usuario = mtu.ref_cod_tipo_usuario)
              where lower(nm_submenu) like '%'||lower($1)||'%' AND
                    arquivo is not null AND
                    trim(arquivo) <> '' AND
                    mtu.visualiza = 1 AND
                    u.cod_usuario = '{$usuario}'
              limit 15";

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
