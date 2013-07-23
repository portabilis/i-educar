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

require_once 'include/modules/clsModulesRota.inc.php';

require_once 'Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/Date/Utils.php';

class RotaController extends ApiCoreController
{
  protected $_processoAp        = 578; //verificar
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA; // verificar

  protected function loadNomePessoaj($id) {
    $sql  = "select nome from cadastro.pessoa, modules.empresa_transporte_escolar emp where idpes = emp.ref_idpes and cod_empresa_transporte_escolar = $1";
    $nome = $this->fetchPreparedQuery($sql, $id, false, 'first-field');

    return $this->toUtf8($nome, array('transform' => true));
  }    

  protected function createOrUpdateRota($id = null){
    

    $rota                          = new clsModulesRota();
    $rota->cod_rota = $id;


    // após cadastro não muda mais id pessoa
    $rota->descricao                     = $this->getRequest()->descricao;
    $rota->ref_idpes_destino                 = $this->getRequest()->pessoaj_id;
    $rota->ano                     = $this->getRequest()->ano;

    return (is_null($id) ? $rota->cadastra() : $rota->edita());
  }


  protected function get() {


      $id               = $this->getRequest()->id;
      $rota            = new clsModulesRota();
      $rota->cod_rota = $id;
      $rota            = $rota->detalhe();

      $attrs  = array(
        'cod_rota'  => 'id',
        'descricao' => 'descricao',
        'ref_idpes_destino'        => 'ref_idpes_destino',
        'ano' => 'ano'
      );

      $rota = Portabilis_Array_Utils::filter($rota, $attrs);

      return $rota;


  }

  protected function post() {

    $id = $this->createOrUpdateRota();

    if (is_numeric($id)) {

      $this->messenger->append('Cadastro realizado com sucesso', 'success', false, 'error');
    }
    else
      $this->messenger->append('Aparentemente a rota não pode ser cadastrada, por favor, verifique.');
   

    return array('id' => $id);
  }

  protected function put() {
      $id = $this->getRequest()->id;
      $editou = $this->createOrUpdateRota();

      if ($editou) {

        $this->messenger->append('Alteração realizada com sucesso', 'success', false, 'error');
      }
      else
        $this->messenger->append('Aparentemente a rota não pode ser alterado, por favor, verifique.');
   

    return array('id' => $id);
  }

  protected function delete() {
    $id = $this->getRequest()->id;


      $rota                  = new clsModulesRota();
      $rota->cod_rota       = $id;
      

      if($rota->excluir()){
        $this->messenger->append('Cadastro removido com sucesso', 'success', false, 'error');
      }else
        $this->messenger->append('Aparentemente o cadastro não pode ser removido, por favor, verifique.',
                                 'error', false, 'error');
    

    return array('id' => $id);
  }


  public function Gerar() {
    

    if ($this->isRequestFor('get', 'rota'))
      $this->appendResponse($this->get());

    // create
    elseif ($this->isRequestFor('post', 'rota'))
      $this->appendResponse($this->post());

    // update
    elseif ($this->isRequestFor('put', 'rota'))
      $this->appendResponse($this->put());

    elseif ($this->isRequestFor('delete', 'rota'))
      $this->appendResponse($this->delete());

    else
      $this->notImplementedOperationError();
  }
}
