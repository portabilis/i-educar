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
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'include/modules/clsModulesEmpresaTransporteEscolar.inc.php';

require_once 'Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/Date/Utils.php';

class EmpresaController extends ApiCoreController
{
  protected $_processoAp        = 578; //verificar
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA; // verificar

  // validation

  protected function validatePessoaJuridica($id){
    
    
  }

  // load resources

  protected function loadNomePessoa($id) {
    $sql  = "select nome from cadastro.pessoa, modules.empresa_transporte_escolar where idpes = ref_idpes and cod_empresa_transporte_escolar = $1";
    $nome = $this->fetchPreparedQuery($sql, $id, false, 'first-field');

    return $this->toUtf8($nome, array('transform' => true));
  }



  protected function createOrUpdateEmpresa($id = null){
    

    $empresa                                 = new clsModulesEmpresaTransporteEscolar();
    $empresa->cod_empresa_transporte_escolar = $id;


    // após cadastro não muda mais id pessoa
    $empresa->ref_resp_idpes                 = $this->getRequest()->pessoa_id;
    $empresa->ref_idpes                      = $this->getRequest()->pessoaj;
    $empresa->observacao                     = $this->getRequest()->observacao;

    return (is_null($id) ? $empresa->cadastra() : $empresa->edita());
  }


  protected function get() {
    if ($this->canGet()){
      $id               = $this->getRequest()->id;
      $empresa            = new clsModulesEmpresaTransporteEscolar();
      $empresa->cod_empresa_transporte_escolar = $id;
      $empresa            = $empresa->detalhe();

      $attrs  = array(
        'cod_empresa_transporte_escolar'  => 'id',
        'ref_idpes' => 'pessoaj',
        'observacao'        => 'observacao',
        'ref_resp_idpes' => 'pessoa'
      );

      $empresa = Portabilis_Array_Utils::filter($empresa, $attrs);

      $empresa['nome']             = $this->loadNomePessoa($id);
      return $empresa;
    }

  }


  protected function canGet(){

    $id = $this->getRequest()->id;
    $empresa            = new clsModulesEmpresaTransporteEscolar();
    $empresa->cod_empresa_transporte_escolar = $id;
    if ($empresa->existe())    
      return true;
    else
      return false;
  }

  protected function post() {

      $id = $this->createOrUpdateEmpresa();

      if (is_numeric($id)) {

        $this->messenger->append('Cadastrado realizado com sucesso', 'success', false, 'error');
      }
      else
        $this->messenger->append('Aparentemente o aluno não pode ser cadastrado, por favor, verifique.');
   

    return array('id' => $id);
  }

  protected function put() {
      $id = $this->getRequest()->id;
      $editou = $this->createOrUpdateEmpresa();

      if ($editou) {

        $this->messenger->append('Alteração realizada com sucesso', 'success', false, 'error');
      }
      else
        $this->messenger->append('Aparentemente o aluno não pode ser alterado, por favor, verifique.');
   

    return array('id' => $id);
  }


  protected function enable() {
    $id = $this->getRequest()->id;

    if ($this->canEnable()) {
      $aluno                  = new clsPmieducarAluno();
      $aluno->cod_aluno       = $id;
      $aluno->ref_usuario_exc = $this->getSession()->id_pessoa;
      $aluno->ativo           = 1;

      if($aluno->edita())
        $this->messenger->append('Cadastro ativado com sucesso', 'success', false, 'error');
      else
        $this->messenger->append('Aparentemente o cadastro não pode ser ativado, por favor, verifique.',
                                 'error', false, 'error');
    }

    return array('id' => $id);
  }

  protected function delete() {
    $id = $this->getRequest()->id;


      $empresa                  = new clsModulesEmpresaTransporteEscolar();
      $empresa->cod_empresa_transporte_escolar       = $id;
      

      if($empresa->excluir()){
        $this->messenger->append('Cadastro removido com sucesso', 'success', false, 'error');
      }else
        $this->messenger->append('Aparentemente o cadastro não pode ser removido, por favor, verifique.',
                                 'error', false, 'error');
    

    return array('id' => $id);
  }


  public function Gerar() {
    if ($this->isRequestFor('get', 'empresa'))
      $this->appendResponse($this->get());

    // create
    elseif ($this->isRequestFor('post', 'empresa'))
      $this->appendResponse($this->post());

    // update
    elseif ($this->isRequestFor('put', 'empresa'))
      $this->appendResponse($this->put());

    elseif ($this->isRequestFor('delete', 'empresa'))
      $this->appendResponse($this->delete());

    else
      $this->notImplementedOperationError();
  }
}
