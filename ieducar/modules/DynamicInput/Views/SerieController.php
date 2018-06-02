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
 * @package   Avaliacao
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Business/Professor.php';

/**
 * CursoController class.
 *
 * @author      Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão ?
 * @version     @@package_version@@
 */
class SerieController extends ApiCoreController
{

  protected function canGetSeries() {
    return $this->validatesId('instituicao') &&
           $this->validatesId('curso') &&
           $this->validatesId('escola');
  }

  protected function getSeries() {
    if ($this->canGetSeries()) {
      $userId               = $this->getSession()->id_pessoa;
      $instituicaoId        = $this->getRequest()->instituicao_id;
      $escolaId             = $this->getRequest()->escola_id;
      $cursoId              = $this->getRequest()->curso_id;

      $isProfessor          = Portabilis_Business_Professor::isProfessor($instituicaoId, $userId);
      $canLoadSeriesAlocado = Portabilis_Business_Professor::canLoadSeriesAlocado($instituicaoId);   

      if ($isProfessor && $canLoadSeriesAlocado){
        $resources = Portabilis_Business_Professor::seriesAlocado($instituicaoId, $escolaId, $cursoId, $userId);
        $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'id', 'nome');
      }elseif ($escolaId && $cursoId && empty($resources))
        $resources = App_Model_IedFinder::getSeries($instituicaoId = null, $escolaId, $cursoId);

      $options = array();

      foreach ($resources as $serieId => $serie)
        $options['__' . $serieId] = $this->toUtf8($serie);

      return array('options' => $options);
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'series'))
      $this->appendResponse($this->getSeries());
    else
      $this->notImplementedOperationError();
  }
}