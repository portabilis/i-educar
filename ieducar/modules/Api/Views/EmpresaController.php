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
require_once 'include/modules/clsModulesRotaTransporteEscolar.inc.php';
require_once 'include/modules/clsModulesMotorista.inc.php';

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

  protected function loadNomePessoaj($id) {
    $sql  = "select nome from cadastro.pessoa, modules.empresa_transporte_escolar emp where idpes = emp.ref_idpes and cod_empresa_transporte_escolar = $1";
    $nome = $this->fetchPreparedQuery($sql, $id, false, 'first-field');

    return $this->toUtf8($nome, array('transform' => true));
  }    



  protected function createOrUpdateEmpresa($id = null){
    

    $empresa                                 = new clsModulesEmpresaTransporteEscolar();
    $empresa->cod_empresa_transporte_escolar = $id;


    $empresa->ref_resp_idpes                 = $this->getRequest()->pessoa_id;
    $empresa->ref_idpes                      = $this->getRequest()->pessoaj_id;
    $empresa->observacao                     = Portabilis_String_Utils::toLatin1($this->getRequest()->observacao);

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
      $empresa['pessoajnome']      = $this->loadNomePessoaj($id);
      $empresa['observacao']       = Portabilis_String_Utils::toUtf8($empresa['observacao']);
      return $empresa;
    }

  }

  protected function validateIfEmpresaIsNotInUse(){

      $id = $this->getRequest()->id;

      $pt = new clsModulesRotaTransporteEscolar();
      $lista = $pt->lista(null,null,null,null,null,$id );
      if(is_array($lista) && count($lista)>0){
        $this->messenger->append('Não é possível excluir uma empresa que está vinculada a uma rota.',
                                 'error', false, 'error');
        return false;
      }else{
        $motorista = new clsModulesMotorista();
        $lst = $motorista->lista(null,null,null,null,$id );

        if(is_array($lst) && count($lst)>0){
          $this->messenger->append('Não é possível excluir uma empresa que está vinculada a um motorista.',
                                 'error', false, 'error');
          return false;
        }else
          return true;
      }
  }

  protected function sqlsForNumericSearch() {

    $sqls[] = "select distinct cod_empresa_transporte_escolar as id, nome as name from
                 modules.empresa_transporte_escolar, cadastro.pessoa where idpes = ref_idpes
                 and cod_empresa_transporte_escolar like $1||'%'";

    return $sqls;
  }


  protected function sqlsForStringSearch() {

    $sqls[] = "select distinct cod_empresa_transporte_escolar as id, nome as name from
                 modules.empresa_transporte_escolar, cadastro.pessoa where idpes = ref_idpes
                 and lower((nome)) like '%'||lower(($1))||'%'";

    return $sqls;
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

    protected function validateSizeOfObservacao(){

      if (strlen($this->getRequest()->observacao)<=255)
        return true;
      else{
       $this->messenger->append('O campo Observações não pode ter mais que 255 caracteres.');
       return false;
      }
      

    }

  protected function post() {


    if ($this->validateSizeOfObservacao()){

      $id = $this->createOrUpdateEmpresa();

      if (is_numeric($id)) {

        $this->messenger->append('Cadastro realizado com sucesso', 'success', false, 'error');
      }
      else
        $this->messenger->append('Aparentemente a empresa não pode ser cadastrada, por favor, verifique.');
    }
   

    return array('id' => $id);
  }

  protected function put() {

    if ($this->validateSizeOfObservacao()){
      $id = $this->getRequest()->id;
      $editou = $this->createOrUpdateEmpresa($id);

      if ($editou) {

        $this->messenger->append('Alteração realizada com sucesso', 'success', false, 'error');
      }
      else
        $this->messenger->append('Aparentemente a empresa não pode ser alterado, por favor, verifique.');
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
    elseif($this->isRequestFor('get', 'empresa-search'))
      $this->appendResponse($this->search());
    // create
    elseif ($this->isRequestFor('post', 'empresa'))
      $this->appendResponse($this->post());

    // update
    elseif ($this->isRequestFor('put', 'empresa'))
      $this->appendResponse($this->put());

    elseif ($this->isRequestFor('delete', 'empresa')){
        if ($this->validateIfEmpresaIsNotInUse()){
          $this->appendResponse($this->delete());
          echo "<script language= \"JavaScript\">
                location.href=\"intranet/transporte_empresa_lst.php\";
              </script>";
          die();
        }                
        
    }else
      $this->notImplementedOperationError();
  }
}
