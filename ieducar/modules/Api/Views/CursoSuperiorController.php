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

/**
 * Class CursoSuperiorController
 * @deprecated Essa versão da API pública será descontinuada
 */
class CursoSuperiorController extends ApiCoreController
{
  // search options

  protected function sqlsForStringSearch() {

    $sqls[] = "SELECT id,
                      curso_id,
                      nome || ' / ' || coalesce((CASE grau_academico
                                                      WHEN 1 THEN 'Tecnológico'
                                                      WHEN 2 THEN 'Licenciatura'
                                                      WHEN 3 THEN 'Bacharelado' END), '') AS name
                 FROM modules.educacenso_curso_superior 
                WHERE unaccent(nome) ILIKE '%'|| unaccent($1) ||'%'
                   OR curso_id ILIKE '%'|| $1 ||'%'
                LIMIT 15";

    return $sqls;
  }

  protected function formatResourceValue($resource) {
    return $resource['curso_id'] . ' - ' . $this->toUtf8($resource['name'], array('transform' => true));
  }

  // sobrescrito para pesquisar apenas string, pois o codigo do curso possui letras
  protected function loadResourcesBySearchQuery($query) {
    $results      = array();
    $sqls   = $this->sqlsForStringSearch();
    $params = $this->sqlParams($query);

    if (! is_array($sqls))
      $sqls = array($sqls);

    foreach($sqls as $sql) {
      $_results = $this->fetchPreparedQuery($sql, $params, false);

      foreach($_results as $result) {
        if (! isset($results[$result['id']]))
          $results[$result['id']] = $this->formatResourceValue($result);
      }
    }

    return $results;
  }

  protected function searchOptions() {
    return array('namespace' => 'modules', 'table' => 'educacenso_curso_superior', 'idAttr' => 'id');
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'cursosuperior-search'))
      $this->appendResponse($this->search());
    else
      $this->notImplementedOperationError();
  }
}
