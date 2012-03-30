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

class ReservaApiController extends ApiCoreController
{
  protected $_dataMapper  = '';#Avaliacao_Model_NotaComponenteDataMapper';
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_BIBLIOTECA;
  protected $_saveOption  = FALSE;
  protected $_deleteOption  = FALSE;
  protected $_titulo   = '';

  #TODO setar código processoAP, copiar da funcionalidade de reserva existente?
  protected $_processoAp  = 0;

  // validadores especificos reserva

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


  protected function validatesPresenceOfRefCodAcervo(){
    return $this->validator->validatesPresenceOf($this->getRequest()->ref_cod_acervo, 'ref_cod_acervo');
  }


  protected function validatesPresenceOfExemplarId(){
    return $this->validator->validatesPresenceOf($this->getRequest()->exemplar_id, 'exemplar_id');
  }


  // validações negócio

  protected function canAcceptRequest() {
    return parent::canAcceptRequest() &&
           $this->validatesPresenceOfRefCodInstituicao() &&
           $this->validatesPresenceOfRefCodEscola() &&
           $this->validatesPresenceOfRefCodBiblioteca() &&
           $this->validatesPresenceOfRefCodCliente() &&
           $this->validatesPresenceOfRefCodAcervo();
          // TODO validar se cliente da biblioteca
  }


  protected function canPostReserva() {
    return $this->validatesClienteIsNotSuspenso() &&
           $this->validatesPresenceOfExemplarId() &&
           $this->validatesSituacaoExemplarIsIn(array('emprestado', 'reservado', 'emprestado_e_reservado'));
           // TODO qtd reservas em aberto do cliente <= limite biblioteca
           // TODO valor R$ multas em aberto do cliente <= limite biblioteca
           // TODO não existe reserva do exemplar em aberto para o cliente
  }


  protected function validatesSituacaoExemplarIsIn($situacoes) {
    if (! is_array($situacoes))
      $situacoes = array($situacoes);

    $situacaoAtual = $this->getSituacaoForExemplar();
    $situacaoAtual = $situacaoAtual['flag'];
    $msg = "Situação do exemplar deve estar em (" . implode(', ', $situacoes) . ") porem atualmente é $situacaoAtual.";

    return $this->validator->validatesValueInSetOf($situacaoAtual, $situacoes, 'situação', false, $msg);
  }


  protected function validatesClienteIsNotSuspenso() {
    $cliente = $this->getCliente();

    if($cliente['suspenso']) {
      $this->messenger->append("O cliente esta suspenso", 'error');
      return false;
    }

    return true;
  }


  protected function getAvailableOperationsForResources() {
    return array('exemplares' => array('get'),
                 'reserva'    => array('post')
    );
  }


  protected function getExemplar($id = '', $reload = false) {
    if ($reload || ! isset($this->_exemplar)) {

      if (empty($id))
        $id = $this->getRequest()->exemplar_id;

      $exemplar         = new clsPmieducarExemplar($id);
      $exemplar         = $exemplar->detalhe();

      $situacaoExemplar = $this->_getSituacaoForExemplar($exemplar);
      $this->_exemplar  = array('id'         => $exemplar['cod_exemplar'],
                                'situacao'   => $situacaoExemplar,
                                'pendencias' => $this->_getPendenciasForExemplar($exemplar, $situacaoExemplar)
      );
    }

    return $this->_exemplar;
  }


  protected function getDataPrevistaDisponivelForExemplar($dataInicio, $exemplar = null) {
    if (is_null($exemplar))
      $exemplar = $this->getExemplar();

    $qtdDiasEmprestimo = $this->getQtdDiasEmprestimoForExemplar($exemplar);
    $date = date('d/m/Y', strtotime("+$qtdDiasEmprestimo days", strtotime($dataInicio)));

    return $date;
  }


  protected function getQtdDiasEmprestimoForExemplar($exemplar = null) {
    if (is_null($exemplar))
      $exemplar = $this->getExemplar();

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


  protected function _getPendenciasForExemplar($exemplar, $situacaoExemplar = '') {
    if (empty($situacaoExemplar))
      $situacaoExemplar = $this->_getSituacaoForExemplar($exemplar);

    $pendencias = array();

    if (strpos($situacaoExemplar['flag'], 'emprestado') > -1)
      $pendencias[] = $this->getEmprestimoForExemplar($exemplar);

    if (strpos($situacaoExemplar['flag'], 'reservado') > -1)
      $pendencias[] = $this->getReservaForExemplar($exemplar);

    return $pendencias;
  }


  protected function _getSituacaoForExemplar($exemplar) {
    $situacao                  = $this->getSituacaoById($exemplar["ref_cod_situacao"]);

    $reservado                 = $this->existsReservaForExemplar($exemplar);
    $emprestado                = $situacao["situacao_emprestada"] == 1;

    $situacaoPermiteEmprestimo = $situacao["permite_emprestimo"]  == 2;
    $exemplarPermiteEmprestimo = $exemplar["permite_emprestimo"]  == 2;

    if ($emprestado && $reservado)
      $flagSituacaoExemplar = 'emprestado_e_reservado';
    elseif ($emprestado)
      $flagSituacaoExemplar = 'emprestado';
    elseif ($reservado)
      $flagSituacaoExemplar =  'reservado';
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
      'reservado'              => array('flag'  => 'reservado'   , 'label' => 'Reservado'   ),
      'emprestado_e_reservado' => array('flag'  => 'emprestado_e_reservado',
                                        'label' => 'Emprestado e reservado'                ),
      'invalida'               => array('flag'  => 'invalida'    , 'label' => 'Inválida'    )
    );

    return $situacoes[$flag];
  }


  protected function getSituacaoForExemplar($exemplar = null) {
    if (is_null($exemplar))
      $exemplar = $this->getExemplar();

    return $exemplar['situacao'];
  }


  protected function getSituacaoById($id) {
    $situacao = new clsPmieducarSituacao($id);
    return $situacao->detalhe();
  }


  protected function getAcervo($id = '') {
    if (empty($id))
      $id = $this->getRequest()->ref_cod_acervo;

    $acervo = new clsPmieducarAcervo($id);
    return $acervo->detalhe();
  }


  protected function getEmprestimoForExemplar($exemplar = null) {
    if (is_null($exemplar))
      $exemplar = $this->getExemplar();

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
      $cliente                                 = $this->getCliente($emprestimo["ref_cod_cliente"]);

      $_emprestimo['exists']                   = true;
      $_emprestimo['data']                     = date('d/m/Y', strtotime($emprestimo['data_retirada']));

      $_emprestimo['data_prevista_disponivel'] = $this->getDataPrevistaDisponivelForExemplar($emprestimo['data_retirada'], $exemplar);
      $_emprestimo['cliente']                  = $cliente;
      $_emprestimo['nome_cliente']             = $cliente['id'] . ' - ' . $cliente['nome'];
    }

    return $_emprestimo;
  }


  protected function existsReservaForExemplar($exemplar = null) {
    $reserva = $this->getReservaForExemplar($exemplar, $reload = true);
    return $reserva['exists'];
  }


  protected function getReservaForExemplar($exemplar = null, $reload = false) {

    if ($reload || ! isset($this->_reserva)) {
      if (is_null($exemplar))
        $exemplar = $this->getExemplar();

      $this->_reserva = array('cliente'                  => null,
                              'nome_cliente'             => '',
                              'data'                     => '',
                              'data_prevista_disponivel' => '',
                              'exists'                   => false,
                              'situacao'                 => $this->getSituacaoForFlag('reservado')
      );


		  $reserva = new clsPmieducarReservas();
		  $reserva = $reserva->lista(null,
                                 null,
                                 null,
                                 null,
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
                                 $this->getRequest()->ref_cod_escola);

		  if(is_array($reserva) && ! empty($reserva)) {
			  $reserva                            = array_shift($reserva);
        $cliente                            = $this->getCliente($reserva["ref_cod_cliente"]);
        //$dataPrevistaDisponivel             = date('d/m/Y', strtotime($reserva['data_prevista_disponivel']));

        $dataPrevistaDisponivel             = $reserva['data_prevista_disponivel'];

        $this->_reserva['exists']           = true;
        $this->_reserva['data']             = date('d/m/Y', strtotime($reserva['data_reserva']));

        $this->_reserva['data_prevista_disponivel'] = $this->getDataPrevistaDisponivelForExemplar($dataPrevistaDisponivel, $exemplar);
        $this->_reserva['cliente']          = $cliente;
        $this->_reserva['nome_cliente']     = $cliente['id'] . ' - ' . $cliente['nome'];
      }
    }

    return $this->_reserva;
  }


  protected function getCliente($id = '') {

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


  // metódos resposta operação / recurso

  protected function getExemplares() {

		$exemplares = new clsPmieducarExemplar();
    $exemplares = $exemplares->lista(null,
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

    $_exemplares = array();

    foreach($exemplares as $exemplar) {
      $_exemplares[] = $this->getExemplar($exemplar['cod_exemplar'], $reload = true);
    }

    return $_exemplares;
  }


  protected function postReserva() {
    if ($this->canPostReserva()) {
      //TODO try pegar excessoes no post, se pegar add msg erro inesperado

      // TODO gravar reserva

        $this->messenger->append("Reserva realizada com sucesso.", 'success');
      //TODO fim try

      $exemplar = $this->getExemplar();

      $this->appendResponse('situacao_exemplar', $exemplar['situacao']);
      $this->appendResponse('pendencias', $exemplar['pendencias']);
    }
  }


  public function Gerar() {
    if ($this->isRequestFor('get', 'exemplares'))
      $this->appendResponse('exemplares', $this->getExemplares());

    elseif ($this->isRequestFor('post', 'reserva'))
      $this->postReserva();

    else
      $this->notImplementedOperationError();
  }
}
