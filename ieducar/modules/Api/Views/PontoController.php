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

require_once 'include/modules/clsModulesPontoTransporteEscolar.inc.php';

require_once 'Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Date/Utils.php';

class PontoController extends ApiCoreController
{
  protected $_processoAp        = 578; //verificar
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA; // verificar
   
  protected function createOrUpdatePonto($id = null){
    
    $ponto                          = new clsModulesPontoTransporteEscolar();
    $ponto->cod_ponto_transporte_escolar = $id;

    // após cadastro não muda mais id pessoa
    $ponto->descricao                     = Portabilis_String_Utils::toLatin1($this->getRequest()->descricao);

    return (is_null($id) ? $ponto->cadastra() : $ponto->edita());
  }


  protected function get() {

      $id                   = $this->getRequest()->id;
      $ponto                 = new clsModulesPontoTransporteEscolar();
      $ponto->cod_ponto_transporte_escolar       = $id;
      $ponto                 = $ponto->detalhe();

      $attrs  = array(
        'cod_ponto_transporte_escolar'  => 'id',
        'descricao' => 'descricao'
      );

      $ponto = Portabilis_Array_Utils::filter($ponto, $attrs);

      return $ponto;
  }

  protected function post() {

    $id = $this->createOrUpdatePonto();
    if (is_numeric($id)) {
      $this->messenger->append('Cadastro realizado com sucesso', 'success', false, 'error');
    }
    else
      $this->messenger->append('Aparentemente o ponto não pode ser cadastrada, por favor, verifique.');
   
    return array('id' => $id);
 }

  protected function put() {
      $id = $this->getRequest()->id;
      $editou = $this->createOrUpdatePonto($id);

      if ($editou) {

        $this->messenger->append('Alteração realizada com sucesso', 'success', false, 'error');
      }
      else
        $this->messenger->append('Aparentemente a rota não pode ser alterado, por favor, verifique.');
   

    return array('id' => $id);
  }

  protected function delete() {
    $id = $this->getRequest()->id;


    $ponto                  = new clsModulesPontoTransporteEscolar();
    $ponto->cod_ponto_transporte_escolar       = $id;
      
    if($ponto->excluir()){
     $this->messenger->append('Cadastro removido com sucesso', 'success', false, 'error');
    }else
      $this->messenger->append('Aparentemente o cadastro não pode ser removido, por favor, verifique.',
                               'error', false, 'error');
    

    return array('id' => $id);
  }


  public function Gerar() {
    
    if ($this->isRequestFor('get', 'ponto'))
      $this->appendResponse($this->get());

    // create
    elseif ($this->isRequestFor('post', 'ponto'))
      $this->appendResponse($this->post());

    // update
    elseif ($this->isRequestFor('put', 'ponto'))
      $this->appendResponse($this->put());

    elseif ($this->isRequestFor('delete', 'ponto')){
        $this->appendResponse($this->delete());
        // Gambi para o caso de não conseguir redirencionar pelo recurso
       /* echo "<script language= \"JavaScript\">
                location.href=\"intranet/transporte_ponto_lst.php\";
              </script>";*/ 
    }else
      $this->notImplementedOperationError();
  }
}
