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
 * @package   Biblioteca
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'include/pmieducar/clsPmieducarExemplar.inc.php';
require_once 'lib/Portabilis/Array/Utils.php';

class EmprestimoApiController extends ApiCoreController
{
  protected $_dataMapper  = '';#Avaliacao_Model_NotaComponenteDataMapper';
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_BIBLIOTECA;
  protected $_saveOption  = FALSE;
  protected $_deleteOption  = FALSE;
  protected $_titulo   = '';

  #TODO setar código processoAP, copiar da funcionalidade de emprestimo existente?
  protected $_processoAp  = 0;


  // validadores especificos emprestimo

  protected function validatesExistenceOfExemplarByTombo() {
    $valid = true;

    if ($this->loadExemplaresByTombo($reload = true) < 1) {
      $this->messenger->append("Não existe um exemplar com tombo '{$this->getRequest()->tombo_exemplar}' " .
                               "para a biblioteca informada.");
      $valid = false;
    }

    return $valid;
  }


  # TODO validar se cliente vinculado a biblioteca? (vinculo pelo tipo de cliente ?)
  protected function validatesExistenceOfCliente() {
    $valid = true;

    if (! $this->loadCliente()) {
      $this->messenger->append("Não existe um cliente com id '{$this->getRequest()->cliente_id}'.");
      $valid = false;
    }

    return $valid;
  }


  // validações negócio

  protected function canAcceptRequest() {
    return parent::canAcceptRequest()

           and $this->validatesPresenceOf(array('instituicao_id',
                                                'escola_id',
                                                'biblioteca_id',
                                                'cliente_id',
                                                'tombo_exemplar'))

           and $this->validatesIsNumeric('tombo_exemplar')
           and $this->validatesExistenceOfExemplarByTombo()
           and $this->validatesExistenceOfCliente();
  }


  protected function canPostEmprestimo() {
    return false;


           /*

              TODO validates cliente is not suspenso
              TODO validates presence of exemplar_id
              TODO validates situacao exemplar is disponivel or is reservado cliente
              TODO qtd emprestimos em aberto do cliente <= limite biblioteca
              TODO valor R$ multas em aberto do cliente <= limite biblioteca
              TODO não existe outro exemplar mesma obra emprestado para cliente

           */
  }


  protected function canDeleteEmprestimo() {
    return false;

    /*

      TODO validates presence of exemplar_id
      TODO validates presence of emprestimo_id
      TODO validates situacao exemplar in emprestado, emprestado_e_reservado
      TODO validates emprestado by cliente

    */
  }


  // subscreve metódo super classe

  protected function getAvailableOperationsForResources() {
    return array('exemplares' => array('get'),
                 'emprestimo' => array('post', 'delete')
    );
  }


  /* metódos auxiliares resposta operação / recurso
    metódos iniciados com load consultam informação no banco de dados
    metódos iniciados com get consultam informação em objetos
  */

  protected function loadCliente($id = null) {
    if (! $id)
      $id = $this->getRequest()->cliente_id;

    // load cliente
		$cliente = new clsPmieducarCliente($id);
		$cliente = $cliente->detalhe();

    if ($cliente) {
      $cliente = Portabilis_Array_Utils::filter($cliente, array('cod_cliente' => 'id',
                                                                'ref_idpes'   => 'pessoa_id'
      ));

      // load pessoa
		  $pessoa          = new clsPessoa_($cliente['pessoa_id']);
		  $pessoa          = $pessoa->detalhe();
      $cliente['nome'] = $pessoa["nome"];

      // load suspensao
      $sql = "select 1 from pmieducar.cliente_suspensao where ref_cod_cliente = $1 and data_liberacao is null and data_suspensao + (dias||' day')::interval >= now()";
      $suspenso = $this->fetchPreparedQuery($sql, $params = array($id), true, 'first-field');

      $cliente['suspenso'] = $suspenso == '1';
    }

    return $cliente;
  }


  protected function loadExemplaresByTombo($reload = false) {
    if ($reload || ! isset($this->_exemplares)) {

		  $this->_exemplares = new clsPmieducarExemplar();
      $this->_exemplares = $this->_exemplares->lista(null,
                                                     null,
                                                     null,
                                                     null,
                                                     null,
                                                     null,
                                                     null,
                                                     null,
                                                     null,
                                                     null,
                                                     null,
                                                     null,
                                                     null,
                                                     1,
                                                     null,
                                                     null,
                                                     null,
                                                     null,
                                                     $this->getRequest()->ref_cod_biblioteca,
                                                     null,
                                                     $this->getRequest()->ref_cod_instituicao,
                                                     $this->getRequest()->ref_cod_escola,
                                                     $this->getRequest()->tombo_exemplar);
    }

    return $this->_exemplares;
  }


  /* metódos resposta operação / recurso
     metódos nomeados no padrão operaçãoRecurso */

  protected function getExemplares() {

    #TODO implementar loadExemplares
    $exemplares = array(); #$this->loadExemplares();
    $_exemplares = array();

    foreach($exemplares as $exemplar) {
      $_exemplares[] = $this->loadExemplar($exemplar['cod_exemplar'], $reload = true);
    }

    return $_exemplares;
  }


  protected function postEmprestimo() {
    if ($this->canPostEmprestimo()) {
    }

    $this->appendResponse('#TODO loadExemplar');
  }


  protected function deleteEmprestimo() {

    if ($this->canDeleteEmprestimo())
      $this->messenger->append("#todo desabilitar emprestimo.", 'notice');

    $this->appendResponse('#TODO loadExemplar');
  }


  public function Gerar() {
    if ($this->isRequestFor('get', 'exemplares'))
      $this->appendResponse('exemplares', $this->getExemplares());

    elseif ($this->isRequestFor('post', 'emprestimo'))
      $this->postEmprestimo();

    elseif ($this->isRequestFor('delete', 'emprestimo'))
      $this->deleteEmprestimo();

    else
      $this->notImplementedOperationError();
  }
}
