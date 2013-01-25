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
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
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

class ComponenteCurricularController extends ApiCoreController
{
  // search options

  protected function searchOptions() {
    return array('namespace' => 'modules', 'idAttr' => 'id');
  }

  // subescreve para pesquisar %query%, e nao query% como por padrão
  protected function sqlsForStringSearch() {
    return "select distinct id, nome as name from modules.componente_curricular
            where lower(to_ascii(nome)) like '%'||lower(to_ascii($1))||'%' order by nome limit 15";
  }

  // subscreve formatResourceValue para não adicionar 'id -' a frente do resultado
  protected function formatResourceValue($resource) {
    return $this->toUtf8(mb_strtoupper($resource['name']));
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'componente_curricular-search'))
      $this->appendResponse($this->search());
    else
      $this->notImplementedOperationError();
  }
}
