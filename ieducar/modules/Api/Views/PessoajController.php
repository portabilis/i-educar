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
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Api
 * @subpackage  Modules
 * @since   07/2013
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';

class PessoajController extends ApiCoreController
{
  
  protected function sqlsForNumericSearch() {
    
    $sqls[] = "select distinct idpes as id, nome as name from
                 cadastro.pessoa where tipo='J' and idpes like $1||'%'";

    return $sqls;
  }

  protected function sqlsForStringSearch() {

    $sqls[] = "select distinct idpes as id, nome as name from
                 cadastro.pessoa where tipo='J' and lower((nome)) like '%'||lower(($1))||'%'";

    return $sqls;
  }  

  public function Gerar() {
    if ($this->isRequestFor('get', 'pessoaj-search'))
      $this->appendResponse($this->search());
    else
      $this->notImplementedOperationError();
  }
}
