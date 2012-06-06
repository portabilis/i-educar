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

    $exemplares = $this->loadExemplares($reload = true);

    if (! is_array($exemplares) || count($exemplares) < 1) {
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
    return $this->validatesPresenceOf(array('exemplar_id'));


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
                                                                'ref_idpes'   => 'pessoa_id'));

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


  protected function getDataPrevistaDisponivelForExemplar($exemplar, $dataInicio, $format = 'd/m/Y') {
    $qtdDiasEmprestimo = $this->loadQtdDiasEmprestimoForExemplar($exemplar);
    $date              = date($format, strtotime("+$qtdDiasEmprestimo days", strtotime($dataInicio)));

    # TODO se data cair em feriado ou dia de não trabalho somando +1 dia

    return $date;
  }


  protected function loadReservasForExemplar($exemplar, $clienteId = null, $reload = false) {
    if ($reload || ! isset($this->_reservas)) {
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

      if($reservas) {
        $reservas = Portabilis_Array_Utils::filterSet($reservas, array('cod_reserva'     => 'id',
                                                                       'data_reserva'    => 'data',
                                                                       'ref_cod_cliente' => 'cliente_id',
                                                                       'data_prevista_disponivel'));

        // adicionada informaçoes adicionais a cada reserva
        foreach($reservas as $index => $reserva) {
          $cliente                 = $this->loadCliente($reserva["cliente_id"]);

          $reserva['cliente']      = $cliente;
          $reserva['nome_cliente'] = $cliente['id'] . ' - ' . $cliente['nome'];
          $reserva['data']         = date('d/m/Y', strtotime($reserva['data']));
          $reserva['situacao']     = $this->getSituacaoForFlag('reservado');

        /* para o cliente da reserva: considera a data prevista disponivel gravada na reserva.
           para outros considera a data prevista disponivel da reserva + a quantidade de dias de emprestimo do exemplar
        */
        if ($this->getRequest()->cliente_id == $cliente['id'])
          $reserva['data_prevista_disponivel'] = date('d/m/Y', strtotime($reserva['data_prevista_disponivel']));

        else
          $reserva['data_prevista_disponivel'] = $this->getDataPrevistaDisponivelForExemplar($exemplar, $reserva['data_prevista_disponivel'], 'd/m/Y');
        } //fim for each
      }

      $this->_reservas = $reservas;
    }

    return $this->_reservas;
  }


  protected function loadEmprestimoForExemplar($exemplar) {
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

    if($emprestimo) {
  	  $emprestimo = array_shift($emprestimo);
      $emprestimo = Portabilis_Array_Utils::filterSet($emprestimo, array('cod_emprestimo' => 'id',
                                                                        'data_retirada'   => 'data',
                                                                        'ref_cod_cliente' => 'cliente_id'));

      // adiciona informações adicionais ao emprestimo
      $cliente                                = $this->loadCliente($emprestimo["ref_cod_cliente"]);

      $emprestimo['cliente']                  = $cliente;
      $emprestimo['nome_cliente']             = $cliente['id'] . ' - ' . $cliente['nome'];
      $emprestimo['situacao']                 = $this->getSituacaoForFlag('emprestado');
      $emprestimo['data']                     = date('d/m/Y', strtotime($emprestimo['data']));
      $emprestimo['data_prevista_disponivel'] = $this->getDataPrevistaDisponivelForExemplar($exemplar, $emprestimo['data_retirada'], 'd/m/Y');
    }

    return $_emprestimo;
  }


  protected function existsReservaForExemplar($exemplar = null, $clienteId = null) {
    $reservas = $this->loadReservasForExemplar($exemplar, $clienteId, $reload = true);
    return is_array($reservas) && count($reservas) > 0;
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


  protected function loadSituacaoForExemplar($exemplar) {
    $situacao                  = new clsPmieducarSituacao($exemplar["situacao_id"]);
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


  protected function loadExemplares($reload = false, $id = null) {
    if ($reload || ! isset($this->_exemplares)) {
		  $exemplares = new clsPmieducarExemplar();

      // filtra por acervo_id e/ou tombo_exemplar (caso tenha recebido tais parametros)
      $exemplares = $exemplares->lista($id,
                                       null,
                                       null,
                                       $this->getRequest()->acervo_id,
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

      if ($exemplares) {
        $exemplares = Portabilis_Array_Utils::filterSet($exemplares, array('cod_exemplar'         => 'id',
                                                                           'ref_cod_fonte'        => 'fonte_id',
                                                                           'ref_cod_motivo_baixa' => 'motivo_baixa_id',
                                                                           'ref_cod_acervo'       => 'acervo_id',
                                                                           'ref_cod_biblioteca'   => 'biblioteca_id',
                                                                           'ref_cod_situacao'     => 'situacao_id',
                                                                           'permite_emprestimo',
                                                                           'tombo'));

        // adiciona situacao e pendencias de cada exemplar
        foreach($exemplares as $index => $exemplar) {
          $situacaoExemplar                 = $this->loadSituacaoForExemplar($exemplar);
          $exemplares[$index]['situacao']   = $situacaoExemplar;
          $exemplares[$index]['pendencias'] = $this->getPendenciasForExemplar($exemplar, $situacaoExemplar);
        }
      }

      $this->_exemplares = $exemplares;
    }

    return $this->_exemplares;
  }


  protected function loadExemplar($reload = false, $id = null) {
    if (! $id)
      $id = $this->getRequest()->exemplar_id;

    return $this->loadExemplares($reload, $id);
  }


  /* metódos resposta operação / recurso
     metódos nomeados no padrão operaçãoRecurso */

  protected function getExemplares() {
    $this->appendResponse('exemplares', $this->loadExemplares($reload = true));
  }


  protected function postEmprestimo() {
    if ($this->canPostEmprestimo())
      $this->appendResponse('exemplar', $this->loadExemplar($reload = true));
  }


  protected function deleteEmprestimo() {

    if ($this->canDeleteEmprestimo())
      $this->messenger->append("#todo deleteEmprestimo.", 'notice');

      $this->appendResponse('exemplar', $this->loadExemplar($reload = true));
  }


  public function Gerar() {
    if ($this->isRequestFor('get', 'exemplares'))
      $this->getExemplares();

    elseif ($this->isRequestFor('post', 'emprestimo'))
      $this->postEmprestimo();

    elseif ($this->isRequestFor('delete', 'emprestimo'))
      $this->deleteEmprestimo();

    else
      $this->notImplementedOperationError();
  }
}
