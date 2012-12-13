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

require_once 'include/pmieducar/clsPmieducarAluno.inc.php';

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Array/Utils.php';

require_once 'Transporte/Model/Responsavel.php';

class AlunoController extends ApiCoreController
{
  protected $_dataMapper  = null;

  #TODO definir este valor com mesmo código cadastro de tipo de exemplar?
  protected $_processoAp  = 0;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_saveOption  = FALSE;
  protected $_deleteOption  = FALSE;
  protected $_titulo   = '';

  protected function validatesPessoaId() {
    $existenceOptions = array('schema_name' => 'cadastro', 'field_name' => 'idpes');

    return  $this->validatesPresenceOf('pessoa_id') &&
            $this->validatesExistenceOf('fisica', $this->getRequest()->pessoa_id, $existenceOptions);
  }

  protected function validatesAlunoId() {
    return  $this->validatesPresenceOf('id') &&
            $this->validatesExistenceOf('aluno', $this->getRequest()->id, $existenceOptions);
  }

  protected function validatesReligiaoId() {
    $isValid = true;

    // beneficio is optional
    if (is_numeric($this->getRequest()->religiao_id)) {
      $isValid = $this->validatesPresenceOf('religiao_id') &&
                 $this->validatesExistenceOf('religiao', $this->getRequest()->religiao_id);
    }

    return $isValid;
  }


    protected function validatesBeneficioId() {
    $isValid = true;

    // beneficio is optional
    if (is_numeric($this->getRequest()->beneficio_id)) {
      $isValid = $this->validatesPresenceOf('beneficio_id') &&
                 $this->validatesExistenceOf('aluno_beneficio', $this->getRequest()->beneficio_id);
    }

    return $isValid;
  }

  protected function validatesResponsavelId() {
    $isValid = true;

    if ($this->getRequest()->tipo_responsavel == 'outra_pessoa') {
      $existenceOptions = array('schema_name' => 'cadastro', 'field_name' => 'idpes');

      $isValid = $this->validatesPresenceOf('responsavel_id') &&
                 $this->validatesExistenceOf('fisica', $this->getRequest()->responsavel_id, $existenceOptions);
    }

    return $isValid;
  }

  protected function validatesResponsavelTipo() {
    $expectedValues = array('mae', 'pai', 'outra_pessoa');

    return $this->validatesPresenceOf('tipo_responsavel') &&
           $this->validator->validatesValueInSetOf($this->getRequest()->tipo_responsavel,
                                                   $expectedValues, 'tipo_responsavel');
  }

  protected function validatesResponsavel() {
    return $this->validatesResponsavelTipo() &&
           $this->validatesResponsavelId();
  }

  protected function validatesTransporte() {
    $expectedValues = array('nenhum', 'municipal', 'estadual');

    return $this->validatesPresenceOf('tipo_transporte') &&
           $this->validator->validatesValueInSetOf($this->getRequest()->tipo_transporte,
                                                   $expectedValues, 'tipo_transporte');
  }

  protected function validatesUniquenessOfAlunoByPessoaId() {
    $existenceOptions = array('schema_name' => 'pmieducar', 'field_name' => 'ref_idpes', 'add_msg_on_error' => false);
    $isValid          = $this->validatesUniquenessOf('aluno', $this->getRequest()->pessoa_id, $existenceOptions);

    if (! $isValid)
      $this->messenger->append("Já existe um aluno cadastrado para a pessoa {$this->getRequest()->pessoa_id}.");

    return $isValid;
  }

  /*protected function validatesUserIsLoggedIn() {
    #FIXME validar tokens API
    return true;
  }*/


  /*protected function canAcceptRequest() {
    return parent::canAcceptRequest();
  }*/

  protected function canGet() {
    return $this->validatesAlunoId();
  }

  protected function _canChange() {
    return $this->canAcceptRequest()           &&
           $this->validatesPessoaId()          &&
           $this->validatesResponsavel()       &&
           $this->validatesTransporte() &&
           $this->validatesReligiaoId()        &&
           $this->validatesBeneficioId();
  }


  protected function canPost() {
    return $this->_canChange() &&
           $this->validatesUniquenessOfAlunoByPessoaId();

           // #TODO continuar validando
  }


  protected function canPut() {
    return $this->_canChange() &&
           $this->validatesAlunoId();

           // #TODO continuar validando
  }


  // load resources

  protected function loadNomeAluno($alunoId = null) {
    if (is_null($alunoId))
      $alunoId = $this->getRequest()->aluno_id;

    $sql = "select nome from cadastro.pessoa, pmieducar.aluno where idpes = ref_idpes and cod_aluno = $1";
    $nome = $this->fetchPreparedQuery($sql, $alunoId, false, 'first-field');

    return $this->safeString($nome);
  }


  protected function loadNameFor($resourceName, $id){
    $sql = "select nm_{$resourceName} from pmieducar.{$resourceName} where cod_{$resourceName} = $1";
    $nome = $this->fetchPreparedQuery($sql, $id, false, 'first-field');

    return $this->safeString($nome);
  }


  protected function loadTransporte($alunoId) {
    $tiposTransporte = array(
      Transporte_Model_Responsavel::NENHUM    => 'nenhum',
      Transporte_Model_Responsavel::MUNICIPAL => 'municipal',
      Transporte_Model_Responsavel::ESTADUAL  =>'estadual'
    );

    $transporteDataMapper = $this->getDataMapperFor('transporte', 'aluno');
    $transporte           = $this->getEntityOf($transporteDataMapper, $alunoId);

    return $tiposTransporte[$transporte->get('responsavel')];
  }


  protected function createOrUpdateTransporte($alunoId) {
    $tiposTransporte = array(
      'nenhum'    => Transporte_Model_Responsavel::NENHUM,
      'municipal' => Transporte_Model_Responsavel::MUNICIPAL,
      'estadual'  => Transporte_Model_Responsavel::ESTADUAL
    );

    $data = array(
      'aluno'       => $alunoId,
      'responsavel' => $tiposTransporte[$this->getRequest()->tipo_transporte],
      'user'        => $this->getSession()->id_pessoa,
      'created_at'  => 'NOW()'
    );

    $transporteDataMapper = $this->getDataMapperFor('transporte', 'aluno');
    $transporte           = $this->getOrCreateEntityOf($transporteDataMapper, $alunoId);
    $transporte->setOptions($data);

    return $this->saveEntity($transporteDataMapper, $transporte);
  }


  protected function loadResponsavel() {
    $pessoa        = new clsFisica();
    $pessoa->idpes = $this->getRequest()->pessoa_id;
    $pessoa        = $pessoa->detalhe();

    return $pessoa->idpes_responsavel;
  }


  protected function updateResponsavel() {
    $pessoa                         = new clsFisica();
    $pessoa->idpes                  = $this->getRequest()->pessoa_id;
    $pessoa->idpes_responsavel      = $this->getRequest()->responsavel_id;

    return $pessoa->edita();
  }


  protected function createOrUpdateAluno($id = null){
    $tiposResponsavel               = array('pai' => 'p', 'mae' => 'm', 'outra_pessoa' => 'r');

    $aluno                          = new clsPmieducarAluno();
    $aluno->cod_aluno               = $id;
    $aluno->ref_idpes               = $this->getRequest()->pessoa_id;
    $aluno->ref_cod_aluno_beneficio = $this->getRequest()->beneficio_id;
    $aluno->ref_cod_religiao        = $this->getRequest()->religiao_id;
    $aluno->analfabeto              = ! is_null($this->getRequest()->alfabetizado) ? 0 : 1;
    $aluno->tipo_responsavel        = $tiposResponsavel[$this->getRequest()->tipo_responsavel];

    return (is_null($id) ? $aluno->cadastra() : $aluno->edita());
  }


  // api responders

  protected function get() {
    if ($this->canGet()) {
      $id               = $this->getRequest()->id;
      $tiposResponsavel = array('p' => 'pai', 'm' => 'mae', 'r' => 'outra_pessoa');

      $aluno            = new clsPmieducarAluno();
      $aluno->cod_aluno = $id;
      $aluno            = $aluno->detalhe();

      $attrs  = array(
        'cod_aluno'               => 'id',
        'ref_cod_aluno_beneficio' => 'beneficio_id',
        'ref_cod_religiao'        => 'religiao_id',
        'ref_idpes'               => 'pessoa_id',
        'tipo_responsavel'        => 'tipo_responsavel',
        'analfabeto'
      );

      $aluno = Portabilis_Array_Utils::filter($aluno, $attrs);

      $aluno['tipo_transporte']  = $this->loadTransporte($id);
      $aluno['tipo_responsavel'] = $tiposResponsavel[$aluno['tipo_responsavel']];
      $aluno['responsavel_id']   = $this->loadResponsavel();
      $aluno['alfabetizado']     = $aluno['analfabeto'] == 1;

      unset($aluno['analfabeto']);

      return $aluno;
    }
  }

  protected function post() {
    if ($this->canPost()) {
      $id = $this->createOrUpdateAluno();

      $this->updateResponsavel();
      $this->createOrUpdateTransporte($id);

      // #TODO set codigo_inep / codigo_rede_ensino_estadual

      if (is_numeric($id))
        $this->messenger->append('Aluno cadastrado com sucesso', 'success');
      else
        $this->messenger->append('Aparentemente o aluno não pode ser cadastrado, por favor, verifique.');

      return array('id' => $id);
    }
  }

  protected function put() {
    $aluno = array();

    if ($this->canPut()) {
      // TODO do somenthing
    }

    return $aluno;
  }


  public function Gerar() {

    if ($this->isRequestFor('get', 'aluno'))
      $this->appendResponse($this->get());

    // creates a new resource
    elseif ($this->isRequestFor('post', 'aluno'))
      $this->appendResponse($this->post());

    // updates a resource
    elseif ($this->isRequestFor('put', 'aluno'))
      $this->appendResponse($this->put());

    else
      $this->notImplementedOperationError();
  }
}
