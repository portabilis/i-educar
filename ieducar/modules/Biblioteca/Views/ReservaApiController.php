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

// TODO migrar novo padrao api controller

class ReservaApiController extends ApiCoreController
{
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_BIBLIOTECA;

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

  protected function validatesPresenceOfReservaId(){
    return $this->validator->validatesPresenceOf($this->getRequest()->reserva_id, 'reserva_id');
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

           $this->validatesSituacaoExemplarIsIn(array('disponivel',
                                                      'emprestado',
                                                      'reservado',
                                                      'emprestado_e_reservado')) &&

           $this->validatesNotExistsReservaEmAbertoForCliente();

           // TODO validar ?
            // qtd reservas em aberto do cliente <= limite biblioteca
            // valor R$ multas em aberto do cliente <= limite biblioteca
  }


  protected function canDeleteReserva() {
    return $this->validatesPresenceOfExemplarId() &&
           $this->validatesPresenceOfReservaId() &&

           $this->validatesSituacaoExemplarIsIn(array('reservado',
                                                      'emprestado_e_reservado')) &&

           $this->validatesExistsReservaEmAbertoForCliente();
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


  protected function validatesNotExistsReservaEmAbertoForCliente() {
    if ($this->existsReservaEmAbertoForCliente()) {
      $this->messenger->append("Este cliente já possui uma reserva em aberto para um exemplar desta obra.", 'error');
      return false;
    }

    return true;
  }


  protected function validatesExistsReservaEmAbertoForCliente() {
    if (! $this->existsReservaEmAbertoForCliente()) {
      $this->messenger->append("O cliente não possui reserva em aberto para este exemplar.", 'error');
      return false;
    }

    return true;
  }


  // subscreve metódo super classe

  protected function getAvailableOperationsForResources() {
    return array('exemplares' => array('get'),
                 'reserva'    => array('post')
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

    if (strpos($situacaoExemplar['flag'], 'reservado') > -1)
      $pendencias = array_merge($pendencias, $this->loadReservasForExemplar($exemplar));

    return $pendencias;
  }


  protected function loadSituacaoForExemplar($exemplar) {
    $situacao                  = new clsPmieducarSituacao($exemplar["ref_cod_situacao"]);
    $situacao                  = $situacao->detalhe();

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


  protected function existsReservaForExemplar($exemplar = null, $clienteId = null) {
    $reservas = $this->loadReservasForExemplar($exemplar, $clienteId, $reload = true);
    return ! empty($reservas) && $reservas[0]['exists'];
  }

  protected function existsReservaEmAbertoForCliente() {
    $clienteId = $this->getRequest()->ref_cod_cliente;
    $exemplares = $this->loadExemplares();

    foreach($exemplares as $exemplar) {
      if ($this->existsReservaForExemplar($exemplar, $clienteId))
        return true;
    }

    return false;
  }

  protected function loadReservasForExemplar($exemplar = null, $clienteId = null, $reload = false) {

    if ($reload || ! isset($this->_reservas)) {
      if (is_null($exemplar))
        $exemplar = $this->loadExemplar();

      $this->_reservas = array();

          $reservas = new clsPmieducarReservas();
          $reservas = $reservas->lista(null,
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

      foreach ($reservas as $reserva) {
        $_reserva = array();
        $cliente                      = $this->loadCliente($reserva["ref_cod_cliente"]);

        $_reserva['exists']           = true;
        $_reserva['reserva_id']       = $reserva['cod_reserva'];
        $_reserva['data']             = date('d/m/Y', strtotime($reserva['data_reserva']));

        /* caso seja o mesmo cliente da reserva: considera a data prevista disponivel gravada na reserva
           senão considera a data prevista disponivel da reserva + a quantidade de dias de emprestimo do exemplar */
        if ($this->getRequest()->ref_cod_escola == $cliente['id'])
          $_reserva['data_prevista_disponivel'] = date('d/m/Y', strtotime($reserva['data_prevista_disponivel']));
        else
          $_reserva['data_prevista_disponivel'] = $this->getDataPrevistaDisponivelForExemplar($reserva['data_prevista_disponivel'], 'd/m/Y', $exemplar);

        $_reserva['cliente']          = $cliente;
        $_reserva['nome_cliente']     = $cliente['id'] . ' - ' . $cliente['nome'];
        $_reserva['situacao']         = $this->getSituacaoForFlag('reservado');

        $this->_reservas[] = $_reserva;
      }
    }

    return $this->_reservas;
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

    // #TODO se data cair em feriado ou dia de não trabalho somar +1 dia ?

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


  protected function postReserva() {
    if ($this->canPostReserva()) {
      $exemplar               = $this->loadExemplar();
      $dataPrevistaDisponivel = $this->getDataPrevistaDisponivelForExemplarAfterLastPendencia($exemplar);

        $reserva = new clsPmieducarReservas(null,
                                          null,
                                          $this->getSession()->id_pessoa,
                                          $this->getRequest()->ref_cod_cliente,
                                          null,
                                          $dataPrevistaDisponivel,
                                          null,
                                          $this->getRequest()->exemplar_id,
                                          1);

          if($reserva->cadastra())
        $this->messenger->append("Reserva realizada com sucesso.", 'success');
      else
        $this->messenger->append("Aparentemente a reserva não foi cadastrada, por favor, tente novamente.", 'error');
    }

    $this->appendResponse($this->loadExemplar($this->getRequest()->exemplar_id, $reload = true));
  }

  protected function deleteReserva() {

    if ($this->canDeleteReserva()) {
      $exemplar = $this->loadExemplar();

      // chamado list para assegurar que esta excluindo a reserva do cliente, biblioteca, instituicao e escola
          $reservas = new clsPmieducarReservas();
          $reservas = $reservas->lista($this->getRequest()->reserva_id,
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

      foreach ($reservas as $reserva) {
            $_reserva = new clsPmieducarReservas($this->getRequest()->reserva_id);

        if($_reserva->excluir())
          $this->messenger->append("Reserva cancelada com sucesso.", 'success');
        else
          $this->messenger->append("Aparentemente a reserva não foi cancelada, por favor, tente novamente.", 'error');
      }

      if(empty($reservas))
        $this->messenger->append("Não foi encontrado uma reserva com os parâmetros recebidos.", 'error');
    }

    $this->appendResponse($this->loadExemplar($this->getRequest()->exemplar_id, $reload = true));
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'exemplares'))
      $this->appendResponse('exemplares', $this->getExemplares());

    elseif ($this->isRequestFor('post', 'reserva'))
      $this->postReserva();

    elseif ($this->isRequestFor('delete', 'reserva'))
      $this->deleteReserva();

    else
      $this->notImplementedOperationError();
  }
}
