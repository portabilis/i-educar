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
 * Class AcervoController
 * @deprecated Essa versão da API pública será descontinuada
 */
class AcervoController extends ApiCoreController
{

  protected function searchOptions() {
    $biblioteca_id = $this->getRequest()->biblioteca_id ? $this->getRequest()->biblioteca_id : 0;

    return array('sqlParams' => array($biblioteca_id));
  }

  protected function formatResourceValue($resource) {
    $nome = $resource['id'] . ' - ' . $this->toUtf8($resource['nome'], array('transform' => true));

    return $nome;
  }

  protected function sqlsForNumericSearch() {
     return "SELECT acervo.cod_acervo as id, initcap(acervo.titulo) as nome
               FROM pmieducar.acervo
              LEFT JOIN pmieducar.acervo_acervo_autor ON (acervo.cod_acervo = acervo_acervo_autor.ref_cod_acervo)
              INNER JOIN pmieducar.exemplar ON (exemplar.ref_cod_acervo = acervo.cod_acervo)
              INNER JOIN pmieducar.biblioteca ON (biblioteca.cod_biblioteca = acervo.ref_cod_biblioteca)
              WHERE (case when $2 = 0 then true else biblioteca.cod_biblioteca = $2 end)
                AND (acervo.cod_acervo::varchar ILIKE '%'||$1||'%' OR acervo.titulo ILIKE '%'||$1||'%')";
  }

  protected function sqlsForStringSearch() {
     return "SELECT acervo.cod_acervo as id, initcap(acervo.titulo) as nome
               FROM pmieducar.acervo
              LEFT JOIN pmieducar.acervo_acervo_autor ON (acervo.cod_acervo = acervo_acervo_autor.ref_cod_acervo)
              INNER JOIN pmieducar.exemplar ON (exemplar.ref_cod_acervo = acervo.cod_acervo)
              INNER JOIN pmieducar.biblioteca ON (biblioteca.cod_biblioteca = acervo.ref_cod_biblioteca)
              WHERE (case when $2 = 0 then true else biblioteca.cod_biblioteca = $2 end)
                AND acervo.titulo ILIKE '%'||$1||'%'";
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'acervo-search'))
      $this->appendResponse($this->search());
    else
      $this->notImplementedOperationError();
  }
}
