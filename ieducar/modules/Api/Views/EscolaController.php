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

class EscolaController extends ApiCoreController
{
  protected $_processoAp        = 561;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;


  protected function canChange() {
    return true;
  }

  protected function loadEscolaInepId($escolaId) {
    $dataMapper = $this->getDataMapperFor('educacenso', 'escola');
    $entity     = $this->tryGetEntityOf($dataMapper, $escolaId);

    return (is_null($entity) ? null : $entity->get('escolaInep'));
  }


  protected function createUpdateOrDestroyEducacensoEscola($escolaId) {
    $dataMapper = $this->getDataMapperFor('educacenso', 'escola');

    if (empty($this->getRequest()->escola_inep_id))
      $result = $this->deleteEntityOf($dataMapper, $escolaId);
    else {
      $data = array(
        'escola'      => $escolaId,
        'escolaInep'  => $this->getRequest()->escola_inep_id,

        // campos deprecados?
        'fonte'      => 'fonte',
        'nomeInep'   => '-',

        // always setting now...
        'created_at' => 'NOW()',
      );

      $entity = $this->getOrCreateEntityOf($dataMapper, $escolaId);
      $entity->setOptions($data);

      $result = $this->saveEntity($dataMapper, $entity);
    }

    return $result;
  }

  protected function get() {
    if ($this->canGet()) {
      $id = $this->getRequest()->id;

      $escola = array();
      $escola['escola_inep_id'] = $this->loadEscolaInepId($id);

      return $escola;
    }
  }

  protected function put() {
    $id = $this->getRequest()->id;

    if ($this->canPut()) {
      $this->createUpdateOrDestroyEducacensoEscola($id);

      $this->messenger->append('Cadastro alterado com sucesso', 'success', false, 'error');
    }
    else
      $this->messenger->append('Aparentemente o cadastro não pode ser alterado, por favor, verifique.',
                               'error', false, 'error');

    return array('id' => $id);
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'escola'))
      $this->appendResponse($this->get());

    elseif ($this->isRequestFor('put', 'escola'))
      $this->appendResponse($this->put());

    else
      $this->notImplementedOperationError();
  }
}