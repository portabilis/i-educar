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
 * @since   07/2013
 * @version   $Id$
 */

require_once 'include/modules/clsModulesMotorista.inc.php';
require_once 'include/modules/clsModulesVeiculo.inc.php';

require_once 'Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/Date/Utils.php';

/**
 * Class MotoristaController
 * @deprecated Essa versão da API pública será descontinuada
 */
class MotoristaController extends ApiCoreController
{
  protected $_processoAp        = 578; //verificar
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA; // verificar

  // load resources

  protected function loadNomePessoa($id) {
    $sql  = "select nome from cadastro.pessoa, modules.motorista where idpes = ref_idpes and cod_motorista = $1";
    $nome = $this->fetchPreparedQuery($sql, $id, false, 'first-field');

    return $this->toUtf8($nome, array('transform' => true));
  }

  protected function loadNomeEmpresa($id) {
    $sql  = "select nome from cadastro.pessoa, modules.motorista ,modules.empresa_transporte_escolar emp where idpes = emp.ref_idpes and cod_empresa_transporte_escolar = ref_cod_empresa_transporte_escolar and cod_motorista = $1";
    $nome = $this->fetchPreparedQuery($sql, $id, false, 'first-field');

    return $this->toUtf8($nome, array('transform' => true));
  }  

  protected function sqlsForNumericSearch() {

    $sqls[] = "select distinct cod_motorista as id, nome as name from
                 modules.motorista, cadastro.pessoa where idpes = ref_idpes
                 and cod_motorista like $1||'%'";

    return $sqls;
  }


  protected function sqlsForStringSearch() {

    $sqls[] = "select distinct cod_motorista as id, nome as name from
                 modules.motorista, cadastro.pessoa where idpes = ref_idpes
                 and lower((nome)) like '%'||lower(($1))||'%'";

    return $sqls;
  }


  protected function createOrUpdateMotorista($id = null){
    

    $motorista                                     = new clsModulesMotorista();
    $motorista->cod_motorista = $id;
    // após cadastro não muda mais id pessoa
    $motorista->ref_idpes                          = $this->getRequest()->pessoa_id;
    $motorista->cnh                                = $this->getRequest()->cnh;
    $motorista->tipo_cnh                           = Portabilis_String_Utils::toLatin1($this->getRequest()->tipo_cnh);
    $motorista->dt_habilitacao                     = Portabilis_Date_Utils::brToPgSQL($this->getRequest()->dt_habilitacao);  
    $motorista->vencimento_cnh                     = Portabilis_Date_Utils::brToPgSQL($this->getRequest()->vencimento_cnh);
    $motorista->ref_cod_empresa_transporte_escolar = $this->getRequest()->empresa_id; 
    $motorista->observacao                         = Portabilis_String_Utils::toLatin1($this->getRequest()->observacao);


    return (is_null($id) ? $motorista->cadastra() : $motorista->edita());
  }

  protected function get() {
    
      $id                                      = $this->getRequest()->id;
      $motorista                               = new clsModulesMotorista();
      $motorista->cod_motorista                = $id;
      $motorista                               = $motorista->detalhe();

      $attrs  = array(
        'cod_motorista'     => 'id',
        'ref_idpes'         => 'pessoa',
        'tipo_cnh'          => 'tipo_cnh',
        'ref_cod_empresa_transporte_escolar' => 'ref_cod_empresa_transporte_escolar',
        'cnh'               => 'cnh',
        'observacao'        => 'observacao',
        'dt_habilitacao'    => 'dt_habilitacao',
        'vencimento_cnh'    => 'vencimento_cnh'
      );

      $motorista = Portabilis_Array_Utils::filter($motorista, $attrs);

      $motorista['nome']             = Portabilis_String_Utils::toUtf8($this->loadNomePessoa($id));
      $motorista['empresa']          = Portabilis_String_Utils::toUtf8($this->loadNomeEmpresa($id));
      $motorista['dt_habilitacao']   = Portabilis_Date_Utils::pgSQLToBr($motorista['dt_habilitacao']);
      $motorista['vencimento_cnh']   = Portabilis_Date_Utils::pgSQLToBr($motorista['vencimento_cnh']);      
      $motorista['cnh']              = Portabilis_String_Utils::toUtf8($motorista['cnh']);
      $motorista['observacao']              = Portabilis_String_Utils::toUtf8($motorista['observacao']);
      return $motorista;

  }

  protected function validateSizeOfObservacao(){
    if (strlen($this->getRequest()->observacao)<=255)
      return true;
    else{
      $this->messenger->append('O campo Observações não pode ter mais que 255 caracteres.');
      return false;
    }

  }

  protected function validateIfMotoristaIsNotInUse(){

      $v = new clsModulesVeiculo();
      $lista = $v->lista(null,null,null,null,null,null,null,null,$this->getRequest()->id);
      if(is_array($lista) && count($lista)>0){
        $this->messenger->append('Não é possível excluir uma motorista responsável por um veículo.',
                                 'error', false, 'error');
        return false;
      }else{
        return true;
      }
  }

  protected function canGet(){
/*
    $id = $this->getRequest()->id;
    $empresa            = new clsModulesEmpresaTransporteEscolar();
    $empresa->cod_empresa_transporte_escolar = $id;
    if ($empresa->existe())    
      return true;
    else
      return false;*/
  }

  protected function post() {

    if ($this->validateSizeOfObservacao()){

      $id = $this->createOrUpdateMotorista();

      if (is_numeric($id)) {

        $this->messenger->append('Cadastro realizado com sucesso', 'success', false, 'error');
      }
      else
        $this->messenger->append('Aparentemente o motorista não pode ser cadastrado, por favor, verifique.');
    }
       

    return array('id' => $id);
  }

  protected function put() {

    if ($this->validateSizeOfObservacao()){
     $id = $this->getRequest()->id;
     $editou = $this->createOrUpdateMotorista($id);

     if ($editou) {
       $this->messenger->append('Alteração realizada com sucesso', 'success', false, 'error');
     }
     else
      $this->messenger->append('Aparentemente o aluno não pode ser alterado, por favor, verifique.');
    }

    return array('id' => $id);
  }

  protected function delete() {
    $id = $this->getRequest()->id;


    $motorista                      = new clsModulesMotorista();
    $motorista->cod_motorista       = $id;
      
    if($motorista->excluir()){
      $this->messenger->append('Cadastro removido com sucesso', 'success', false, 'error');
    }else
      $this->messenger->append('Aparentemente o cadastro não pode ser removido, por favor, verifique.','error', false, 'error');

    return array('id' => $id);
  }


  public function Gerar() {
    
    if ($this->isRequestFor('get', 'motorista'))
      $this->appendResponse($this->get());

    elseif ($this->isRequestFor('get', 'motorista-search'))
      $this->appendResponse($this->search());    

    // create
    elseif ($this->isRequestFor('post', 'motorista'))
      $this->appendResponse($this->post());

    // update
    elseif ($this->isRequestFor('put', 'motorista'))
      $this->appendResponse($this->put());

    elseif ($this->isRequestFor('delete', 'motorista')){
        if ($this->validateIfMotoristaIsNotInUse()){
          $this->appendResponse($this->delete());
          echo "<script language= \"JavaScript\">
                location.href=\"intranet/transporte_motorista_lst.php\";
              </script>";
          die();
        }                
        
    }else
      $this->notImplementedOperationError();
  }
}