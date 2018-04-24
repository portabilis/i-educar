<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Gabriel Matos de Souza <gabriel@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Api
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';

/**
 * Class MenuController
 * @deprecated Essa versão da API pública será descontinuada
 */
class MenuController extends ApiCoreController
{
  private function getCurrentUser(){
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();
    return $this->pessoa_logada;
  }
  protected function sqlsForNumericSearch() {
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

  protected function sqlsForStringSearch() {
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

  protected function formatResourceValue($resource) {
    return $this->toUtf8($resource['name']);
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'menu-search'))
      $this->appendResponse($this->search());
    else
      $this->notImplementedOperationError();
  }
}
