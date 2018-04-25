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

/**
 * Class LogradouroController
 * @deprecated Essa versão da API pública será descontinuada
 */
class LogradouroController extends ApiCoreController
{

  protected function searchOptions() {
    $municipioId = $this->getRequest()->municipio_id ? $this->getRequest()->municipio_id : 0;
    return array('sqlParams'    => array($municipioId), 'selectFields' => array('tipo_logradouro'));

  }

  protected function sqlsForNumericSearch() {

    $sqls[] = "SELECT distinct l.idlog as id, l.nome as name, tl.descricao as tipo_logradouro, m.nome as municipio from
                 public.logradouro l left join urbano.tipo_logradouro tl on (l.idtlog = tl.idtlog)
                 INNER JOIN public.municipio m ON m.idmun = l.idmun
                 where l.idlog::varchar like $1||'%' and (m.idmun = $2 OR $2 = 0)";

    return $sqls;
  }


  protected function sqlsForStringSearch() {

    $sqls[] = "SELECT distinct l.idlog as id, l.nome as name, tl.descricao as tipo_logradouro, m.nome as municipio FROM
                 public.logradouro l left join urbano.tipo_logradouro tl on (l.idtlog = tl.idtlog)
                 INNER JOIN public.municipio m ON m.idmun = l.idmun
                 where (lower((l.nome)) like '%'||lower(($1))||'%'
                 OR lower((tl.descricao))|| ' ' ||lower((l.nome)) like '%'||lower(($1))||'%')
                 and (m.idmun = $2 OR $2 = 0)";

    return $sqls;
  }

  protected function formatResourceValue($resource) {
    $id = $resource['id'];
    $tipo    = $resource['tipo_logradouro'];
    $nome    = $this->toUtf8($resource['name'], array('transform' => true));
    $municipio = $this->toUtf8($resource['municipio'], array('transform' => true));

    return  $this->getRequest()->exibir_municipio ? "$id - $tipo $nome - $municipio": "$tipo $nome";
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'logradouro-search'))
      $this->appendResponse($this->search());
    else
      $this->notImplementedOperationError();
  }

}
