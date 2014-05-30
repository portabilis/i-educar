<?php

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
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'intranet/include/clsBanco.inc.php';

class ServidorController extends ApiCoreController
{

  protected function searchOptions() {
    $escolaId = $this->getRequest()->escola_id ? $this->getRequest()->escola_id : 0;
    return array('sqlParams'    => array($escolaId));
    
  }

  protected function formatResourceValue($resource) {
    $nome    = $this->toUtf8($resource['nome'], array('transform' => true));

    return $nome;
  }

  protected function sqlsForNumericSearch() {
    
    $sqls[] = "SELECT p.idpes as id, p.nome
                FROM cadastro.pessoa p
                INNER JOIN cadastro.fisica f ON (p.idpes = f.idpes)
                INNER JOIN portal.funcionario fun ON (fun.ref_cod_pessoa_fj = f.idpes)
                INNER JOIN pmieducar.servidor s ON (s.cod_servidor = fun.ref_cod_pessoa_fj)
                INNER JOIN pmieducar.servidor_alocacao sa ON (s.cod_servidor = sa.ref_cod_servidor)

                WHERE p.idpes LIKE '%'||$1||'%' AND sa.ref_cod_escola = $2 LIMIT 15";

    return $sqls;
  }

  protected function sqlsForStringSearch() {

    $sqls[] = "SELECT p.idpes as id, p.nome
                FROM cadastro.pessoa p
                INNER JOIN cadastro.fisica f ON (p.idpes = f.idpes)
                INNER JOIN portal.funcionario fun ON (fun.ref_cod_pessoa_fj = f.idpes)
                INNER JOIN pmieducar.servidor s ON (s.cod_servidor = fun.ref_cod_pessoa_fj)
                INNER JOIN pmieducar.servidor_alocacao sa ON (s.cod_servidor = sa.ref_cod_servidor)

                WHERE p.nome LIKE '%'||$1||'%' AND sa.ref_cod_escola = $2 LIMIT 15";

    return $sqls;
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'servidor-search'))
      $this->appendResponse($this->search());
    else
      $this->notImplementedOperationError();
  }

}
