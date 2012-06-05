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

  protected function validatesPresenceOfRefCodInstituicao(){
    return $this->validator->validatesPresenceOf($this->getRequest()->ref_cod_instituicao, 'ref_cod_instituicao');
  }


  protected function validatesPresenceOfRefCodEscola(){
    return $this->validator->validatesPresenceOf($this->getRequest()->ref_cod_escola, 'ref_cod_escola');
  }


  protected function validatesPresenceOfRefCodBiblioteca(){
    return $this->validator->validatesPresenceOf($this->getRequest()->ref_cod_biblioteca, 'ref_cod_biblioteca');
  }


  protected function validatesPresenceOfRefCodCliente(){
    return $this->validator->validatesPresenceOf($this->getRequest()->ref_cod_cliente, 'ref_cod_cliente');
  }


  protected function validatesPresenceOfTombo(){
    return $this->validator->validatesPresenceOf($this->getRequest()->tombo, 'tombo');
  }


  protected function validatesPresenceOfExemplarId(){
    return $this->validator->validatesPresenceOf($this->getRequest()->exemplar_id, 'exemplar_id');
  }

  protected function validatesPresenceOfEmprestimoId(){
    return $this->validator->validatesPresenceOf($this->getRequest()->emprestimo_id, 'emprestimo_id');
  }


  // validações negócio

  protected function canAcceptRequest() {
    return parent::canAcceptRequest() &&
           $this->validatesPresenceOfRefCodInstituicao() &&
           $this->validatesPresenceOfRefCodEscola() &&
           $this->validatesPresenceOfRefCodBiblioteca() &&
           $this->validatesPresenceOfRefCodCliente() &&
           $this->validatesPresenceOfTombo();
          // TODO validar se cliente da biblioteca
  }


  protected function canPostEmprestimo() {
    return $this->validatesClienteIsNotSuspenso() &&
           $this->validatesPresenceOfExemplarId() &&

           $this->validatesSituacaoExemplarIsIn(array('disponivel',
                                                      'emprestado',
                                                      'emprestimodo',
                                                      'emprestado_e_emprestimodo')) &&

           $this->validatesNotExistsEmprestimoEmAbertoForCliente();

           // TODO qtd emprestimos em aberto do cliente <= limite biblioteca
           // TODO valor R$ multas em aberto do cliente <= limite biblioteca
  }


  protected function canDeleteEmprestimo() {
    return $this->validatesPresenceOfExemplarId() &&
           $this->validatesPresenceOfEmprestimoId() &&

           $this->validatesSituacaoExemplarIsIn(array('emprestimodo',
                                                      'emprestado_e_emprestimodo')) &&

           $this->validatesExistsEmprestimoEmAbertoForCliente();
  }


  protected function validatesSituacaoExemplarIsIn($situacoes) {
    if (! is_array($situacoes))
      $situacoes = array($situacoes);

    $situacaoAtual = $this->getSituacaoExemplar();
    $situacaoAtual = $situacaoAtual['flag'];
    $msg = "Situação do exemplar deve estar em (" . implode(', ', $situacoes) . ") porem atualmente é $situacaoAtual.";

    return $this->validator->validatesValueInSetOf($situacaoAtual, $situacoes, 'situação', false, $msg);
  }


  protected function validatesClienteIsNotSuspenso() {
    $cliente = $this->loadCliente();

    if($cliente['suspenso']) {
      $this->messenger->append("O cliente esta suspenso", 'error');
      return false;
    }

    return true;
  }


  protected function validatesNotExistsEmprestimoEmAbertoForCliente() {
    if ($this->existsEmprestimoEmAbertoForCliente()) {
      $this->messenger->append("Este cliente já possui uma emprestimo em aberto para um exemplar desta obra.", 'error');
      return false;
    }

    return true;
  }


  protected function validatesExistsEmprestimoEmAbertoForCliente() {
    if (! $this->existsEmprestimoEmAbertoForCliente()) {
      $this->messenger->append("O cliente não possui emprestimo em aberto para este exemplar.", 'error');
      return false;
    }

    return true;
  }


  // subscreve metódo super classe

  protected function getAvailableOperationsForResources() {
    return array('exemplares' => array('get'),
                 'emprestimo'    => array('post')
    );
  }


  /* metódos auxiliares resposta operação / recurso
    metódos iniciados com load consultam informação no banco de dados
    metódos iniciados com get consultam informação em objetos
  */

  protected function loadExemplar($id = '', $reload = false) {
    if ($reload || ! isset($this->_exemplar)) {

      if (empty($id))
        $id = $this->getRequest()->exemplar_id;

      $exemplar         = new clsPmieducarExemplar($id);
      $exemplar         = $exemplar->detalhe();

      $situacaoExemplar = $this->loadSituacaoForExemplar($exemplar);
      $this->_exemplar  = array('id'         => $exemplar['cod_exemplar'],
                                'situacao'   => $situacaoExemplar,
                                'pendencias' => $this->getPendenciasForExemplar($exemplar, $situacaoExemplar)
      );
    }

    return $this->_exemplar;
  }


  protected function getPendenciasForExemplar($exemplar, $situacaoExemplar = '') {
    if (empty($situacaoExemplar))
      $situacaoExemplar = $exemplar['situacao'];

    $pendencias = array();

    if (strpos($situacaoExemplar['flag'], 'emprestado') > -1)
      $pendencias[] = $this->loadEmprestimoForExemplar($exemplar);

    if (strpos($situacaoExemplar['flag'], 'emprestimodo') > -1)
      $pendencias = array_merge($pendencias, $this->loadEmprestimosForExemplar($exemplar));

    return $pendencias;
  }


  protected function loadSituacaoForExemplar($exemplar) {
    $situacao                  = new clsPmieducarSituacao($exemplar["ref_cod_situacao"]);
    $situacao                  = $situacao->detalhe();

    $emprestimodo                 = $this->existsEmprestimoForExemplar($exemplar);
    $emprestado                = $situacao["situacao_emprestada"] == 1;

    $situacaoPermiteEmprestimo = $situacao["permite_emprestimo"]  == 2;
    $exemplarPermiteEmprestimo = $exemplar["permite_emprestimo"]  == 2;

    if ($emprestado && $emprestimodo)
      $flagSituacaoExemplar = 'emprestado_e_emprestimodo';
    elseif ($emprestado)
      $flagSituacaoExemplar = 'emprestado';
    elseif ($emprestimodo)
      $flagSituacaoExemplar =  'emprestimodo';
    elseif ($situacaoPermiteEmprestimo && $exemplarPermiteEmprestimo)
      $flagSituacaoExemplar = 'disponivel';
    elseif (! $situacaoPermiteEmprestimo || ! $exemplarPermiteEmprestimo)
      $flagSituacaoExemplar = 'indisponivel';
    else
      $flagSituacaoExemplar = 'invalida';

    return $this->getSituacaoForFlag($flagSituacaoExemplar);
  }


  protected function getSituacaoForFlag($flag) {
    $situacoes = array(
      'indisponivel'           => array('flag'  => 'indisponivel', 'label' => 'Indisponível'),
      'disponivel'             => array('flag'  => 'disponivel'  , 'label' => 'Disponível'  ),
      'emprestado'             => array('flag'  => 'emprestado'  , 'label' => 'Emprestado'  ),
      'emprestimodo'              => array('flag'  => 'emprestimodo'   , 'label' => 'Emprestimodo'   ),
      'emprestado_e_emprestimodo' => array('flag'  => 'emprestado_e_emprestimodo',
                                        'label' => 'Emprestado e emprestimodo'                ),
      'invalida'               => array('flag'  => 'invalida'    , 'label' => 'Inválida'    )
    );

    return $situacoes[$flag];
  }


  protected function getSituacaoExemplar($exemplar = null) {
    if (is_null($exemplar))
      $exemplar = $this->loadExemplar();

    return $exemplar['situacao'];
  }


  protected function getAcervo($id = '') {
    if (empty($id))
      $id = $this->getRequest()->ref_cod_acervo;

    $acervo = new clsPmieducarAcervo($id);
    return $acervo->detalhe();
  }


  protected function loadEmprestimoForExemplar($exemplar = null) {
    if (is_null($exemplar))
      $exemplar = $this->loadExemplar();

    $_emprestimo = array('cliente'                => null,
                         'nome_cliente'            => '',
                         'data'                   => '',
                         'data_prevista_disponivel' => '',
                         'exists'                 => false,
                         'situacao'               => $this->getSituacaoForFlag('emprestado')
    );

    $emprestimo = new clsPmieducarExemplarEmprestimo();

    $emprestimo = $emprestimo->lista(null,
                                     null,
                                     null,
                                     null,
                                     $exemplar['cod_exemplar'],
                                     null,
                                     null,
                                     null,
                                     null,
                                     null,
                                     $devolvido = false,
                                     $this->getRequest()->ref_cod_biblioteca,
                                     null,
                                     $this->getRequest()->ref_cod_instituicao,
                                     $this->getRequest()->ref_cod_escola);

    if(is_array($emprestimo) && ! empty($emprestimo)) {
  	  $emprestimo                              = array_shift($emprestimo);
      $cliente                                 = $this->loadCliente($emprestimo["ref_cod_cliente"]);

      $_emprestimo['exists']                   = true;
      $_emprestimo['data']                     = date('d/m/Y', strtotime($emprestimo['data_retirada']));

      $_emprestimo['data_prevista_disponivel'] = $this->getDataPrevistaDisponivelForExemplar($emprestimo['data_retirada'], 'd/m/Y', $exemplar);
      $_emprestimo['cliente']                  = $cliente;
      $_emprestimo['nome_cliente']             = $cliente['id'] . ' - ' . $cliente['nome'];
    }

    return $_emprestimo;
  }


  protected function existsEmprestimoForExemplar($exemplar = null, $clienteId = null) {
    $emprestimos = $this->loadEmprestimosForExemplar($exemplar, $clienteId, $reload = true);
    return ! empty($emprestimos) && $emprestimos[0]['exists'];
  }

  protected function existsEmprestimoEmAbertoForCliente() {
    $clienteId = $this->getRequest()->ref_cod_cliente;
    $exemplares = $this->loadExemplares();

    foreach($exemplares as $exemplar) {
      if ($this->existsEmprestimoForExemplar($exemplar, $clienteId))
        return true;
    }

    return false;
  }

  protected function loadEmprestimosForExemplar($exemplar = null, $clienteId = null, $reload = false) {

    if ($reload || ! isset($this->_emprestimos)) {
      if (is_null($exemplar))
        $exemplar = $this->loadExemplar();

      $this->_emprestimos   = array();

      $sql = "select 1 from pmieducar.cliente_suspensao where ref_cod_cliente = $1 and data_liberacao is null and data_suspensao + (dias||' day')::interval >= now()";

      $suspenso = $this->fetchPreparedQuery($sql, $params = array($id), true, 'first-field');

		  $emprestimos = new clsPmieducarEmprestimos();
		  $emprestimos = $emprestimos->lista(null,
                                   null,
                                   null,
                                   $clienteId,
                                   null,
                                   null,
                                   null,
                                   null,
                                   null,
                                   null,
                                   $exemplar['cod_exemplar'],
                                   1,
                                   $this->getRequest()->ref_cod_biblioteca,
                                   $this->getRequest()->ref_cod_instituicao,
                                   $this->getRequest()->ref_cod_escola,
                                   $data_retirada_null = true);

      foreach ($emprestimos as $emprestimo) {
        $_emprestimo = array();
        $cliente                      = $this->loadCliente($emprestimo["ref_cod_cliente"]);

        $_emprestimo['exists']           = true;
        $_emprestimo['emprestimo_id']       = $emprestimo['cod_emprestimo'];
        $_emprestimo['data']             = date('d/m/Y', strtotime($emprestimo['data_emprestimo']));

        /* caso seja o mesmo cliente da emprestimo: considera a data prevista disponivel gravada na emprestimo
           senão considera a data prevista disponivel da emprestimo + a quantidade de dias de emprestimo do exemplar */
        if ($this->getRequest()->ref_cod_escola == $cliente['id'])
          $_emprestimo['data_prevista_disponivel'] = date('d/m/Y', strtotime($emprestimo['data_prevista_disponivel']));
        else
          $_emprestimo['data_prevista_disponivel'] = $this->getDataPrevistaDisponivelForExemplar($emprestimo['data_prevista_disponivel'], 'd/m/Y', $exemplar);

        $_emprestimo['cliente']          = $cliente;
        $_emprestimo['nome_cliente']     = $cliente['id'] . ' - ' . $cliente['nome'];
        $_emprestimo['situacao']         = $this->getSituacaoForFlag('emprestimodo');

        $this->_emprestimos[] = $_emprestimo;
      }
    }

    return $this->_emprestimos;
  }


  protected function loadCliente($id = '') {

    if (empty($id))
      $id = $this->getRequest()->ref_cod_cliente;

    $_cliente = array('id' => $id);

		$cliente = new clsPmieducarCliente($id);
		$cliente = $cliente->detalhe();

    $_cliente['pessoaId'] = $cliente["ref_idpes"];

		$pessoa = new clsPessoa_($_cliente['pessoaId']);
		$pessoa = $pessoa->detalhe();

    $_cliente['nome']        = $pessoa["nome"];

    $sql = "select 1 from pmieducar.cliente_suspensao where ref_cod_cliente = $1 and data_liberacao is null and data_suspensao + (dias||' day')::interval >= now()";
    $suspenso = $this->fetchPreparedQuery($sql, $params = array($id), true, 'first-field');

    $_cliente['suspenso'] = $suspenso == '1';

    return $_cliente;
  }


  protected function getDataPrevistaDisponivelForExemplar($dataInicio, $format = 'd/m/Y', $exemplar = null) {
    if (is_null($exemplar))
      $exemplar = $this->loadExemplar();

    $qtdDiasEmprestimo = $this->loadQtdDiasEmprestimoForExemplar($exemplar);
    $date = date($format, strtotime("+$qtdDiasEmprestimo days", strtotime($dataInicio)));

    # TODO ver se data cai em feriado ou dia de não trabalho somando +1 dia

    return $date;
  }


  protected function loadQtdDiasEmprestimoForExemplar($exemplar = null) {
    if (is_null($exemplar))
      $exemplar = $this->loadExemplar();

    $acervo             = $this->getAcervo($exemplar['ref_cod_acervo']);
    $exemplarTipoId     = $acervo['ref_cod_exemplar_tipo'];

		$clienteTipoCliente = new clsPmieducarClienteTipoCliente();

    $clienteTipoCliente      = $clienteTipoCliente->lista(null,
                                                          $this->getRequest()->ref_cod_cliente,
                                                          null,
                                                          null,
                                                          null,
                                                          null,
                                                          null,
                                                          null,
                                                          $this->getRequest()->ref_cod_biblioteca,
                                                          1);

    $clienteTipoId           = $clienteTipoCliente[0]['ref_cod_cliente_tipo'];

		$clienteTipoExemplarTipo = new clsPmieducarClienteTipoExemplarTipo($clienteTipoId,
                                                                       $exemplarTipoId);

		$clienteTipoExemplarTipo = $clienteTipoExemplarTipo->detalhe();

		return $clienteTipoExemplarTipo["dias_emprestimo"];
  }


  protected function getDataPrevistaDisponivelForExemplarAfterLastPendencia($exemplar = null) {
    if (is_null($exemplar))
      $exemplar = $this->loadExemplar();

    if (! empty($exemplar['pendencias'])) {
      $lastPendencia          = end($exemplar['pendencias']);
      $dataPrevistaDisponivel = $this->getDataPrevistaDisponivelForExemplar($lastPendencia['data_prevista_disponivel'], 'Y-m-d', $exemplar);
    }
    else
      $dataPrevistaDisponivel = date("Y-m-d");

    return $dataPrevistaDisponivel;
  }


  protected function loadExemplares($reload = false) {
    if ($reload || ! isset($this->_exemplares)) {
		  $this->_exemplares = new clsPmieducarExemplar();
      $this->_exemplares = $this->_exemplares->lista(null,
                                                     null,
                                                     null,
                                                     $this->getRequest()->ref_cod_acervo,
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
                                                     $this->getRequest()->ref_cod_escola);
    }

    return $this->_exemplares;
  }

  /* metódos resposta operação / recurso
     metódos nomeados no padrão operaçãoRecurso */

  protected function getExemplares() {
    $exemplares = $this->loadExemplares();
    $_exemplares = array();

    foreach($exemplares as $exemplar) {
      $_exemplares[] = $this->loadExemplar($exemplar['cod_exemplar'], $reload = true);
    }

    return $_exemplares;
  }


  protected function postEmprestimo() {
    if ($this->canPostEmprestimo()) {
      //TODO try pegar excessoes no post, se pegar add msg erro inesperado

      $exemplar = $this->loadExemplar();

      $dataPrevistaDisponivel = $this->getDataPrevistaDisponivelForExemplarAfterLastPendencia($exemplar);

  		$emprestimo = new clsPmieducarEmprestimos(null,
                                          null,
                                          $this->getSession()->id_pessoa,
                                          $this->getRequest()->ref_cod_cliente,
                                          null,
                                          $dataPrevistaDisponivel,
                                          null,
                                          $this->getRequest()->exemplar_id,
                                          1);

		  if($emprestimo->cadastra())
        $this->messenger->append("Emprestimo realizada com sucesso.", 'success');
      else
        $this->messenger->append("Aparentemente a emprestimo não foi cadastrada, por favor, tente novamente.", 'error');

      //TODO fim try
    }

    $this->appendResponse($this->loadExemplar($this->getRequest()->exemplar_id, $reload = true));
  }

  protected function deleteEmprestimo() {

    if ($this->canDeleteEmprestimo()) {
      $this->messenger->append("#todo desabilitar emprestimo.", 'notice');

      $exemplar = $this->loadExemplar();

      // chamado list para assegurar que esta excluindo a emprestimo do cliente, biblioteca, instituicao e escola
		  $emprestimos = new clsPmieducarEmprestimos();
		  $emprestimos = $emprestimos->lista($this->getRequest()->emprestimo_id,
                                   null,
                                   null,
                                   $this->getRequest()->ref_cod_cliente,
                                   null,
                                   null,
                                   null,
                                   null,
                                   null,
                                   null,
                                   $this->getRequest()->exemplar_id,
                                   1,
                                   $this->getRequest()->ref_cod_biblioteca,
                                   $this->getRequest()->ref_cod_instituicao,
                                   $this->getRequest()->ref_cod_escola,
                                   $data_retirada_null = true);

      foreach ($emprestimos as $emprestimo) {
    		$_emprestimo = new clsPmieducarEmprestimos($this->getRequest()->emprestimo_id);

        if($_emprestimo->excluir())
          $this->messenger->append("Emprestimo cancelada com sucesso.", 'success');
        else
          $this->messenger->append("Aparentemente a emprestimo não foi cancelada, por favor, tente novamente.", 'error');
      }

      if(empty($emprestimos))
        $this->messenger->append("Não foi encontrado uma emprestimo com os parâmetros recebidos.", 'error');
    }

    $this->appendResponse($this->loadExemplar($this->getRequest()->exemplar_id, $reload = true));
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
