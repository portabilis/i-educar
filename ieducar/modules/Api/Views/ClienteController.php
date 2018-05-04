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
 * @author    Caroline Salib <caroline@portabilis.com.br>
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

/**
 * Class ClienteController
 * @deprecated Essa versão da API pública será descontinuada
 */
class ClienteController extends ApiCoreController
{

  // search options

  protected function searchOptions() {
    $escolaId = $this->getRequest()->escola_id ? $this->getRequest()->escola_id : 0;

    return array('sqlParams' => array($escolaId));
  }

  protected function formatResourceValue($resource) {
    $nome    = $this->toUtf8($resource['nome'], array('transform' => true));

    return $nome;
  }

  protected function sqlsForNumericSearch() {
    return "SELECT cliente.cod_cliente as id, initcap(pessoa.nome) as nome
                 FROM pmieducar.cliente
                INNER JOIN pmieducar.cliente_tipo_cliente ON (cliente_tipo_cliente.ref_cod_cliente = cliente.cod_cliente)
                INNER JOIN pmieducar.biblioteca ON (biblioteca.cod_biblioteca = cliente_tipo_cliente.ref_cod_biblioteca)
                INNER JOIN cadastro.pessoa ON (pessoa.idpes = cliente.ref_idpes)
                WHERE (case when $2 = 0 then true else biblioteca.ref_cod_escola = $2 end)
                  AND cliente.cod_cliente ILIKE '%'||$1||'%'";
  }


  protected function sqlsForStringSearch() {
     return "SELECT cliente.cod_cliente as id, initcap(pessoa.nome) as nome
                 FROM pmieducar.cliente
                INNER JOIN pmieducar.cliente_tipo_cliente ON (cliente_tipo_cliente.ref_cod_cliente = cliente.cod_cliente)
                INNER JOIN pmieducar.biblioteca ON (biblioteca.cod_biblioteca = cliente_tipo_cliente.ref_cod_biblioteca)
                INNER JOIN cadastro.pessoa ON (pessoa.idpes = cliente.ref_idpes)
                WHERE (case when $2 = 0 then true else biblioteca.ref_cod_escola = $2 end)
                  AND pessoa.nome ILIKE '%'||$1||'%'";
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'cliente-search'))
      $this->appendResponse($this->search());
    else
      $this->notImplementedOperationError();
  }
}
