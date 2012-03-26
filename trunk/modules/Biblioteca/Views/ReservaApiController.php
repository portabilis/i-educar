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

#require_once 'Core/Controller/Page/EditController.php';
#require_once 'Avaliacao/Model/NotaComponenteDataMapper.php';
#require_once 'Avaliacao/Service/Boletim.php';
#require_once 'App/Model/MatriculaSituacao.php';
#require_once 'RegraAvaliacao/Model/TipoPresenca.php';
#require_once 'RegraAvaliacao/Model/TipoParecerDescritivo.php';
#require_once 'include/pmieducar/clsPmieducarMatricula.inc.php';
#require_once 'include/portabilis/dal.php';
#require_once 'include/pmieducar/clsPmieducarHistoricoEscolar.inc.php';


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


  protected function canAcceptRequest()
  {

    return parent::canAcceptRequest() &&
           $this->validatesPresenceOfRefCodInstituicao() &&
           $this->validatesPresenceOfRefCodEscola() &&
           $this->validatesPresenceOfRefCodBiblioteca() &&
           $this->validatesPresenceOfRefCodCliente() &&
           $this->validatesPresenceOfRefCodAcervo();
          // TODO validar se cliente da biblioteca
  }


  protected function getAvailableOperationsForResources() {
    return array('exemplares' => array('get'),
                 'reserva'    => array('post')
    );
  }


  protected function getExemplares() {

    $_exemplares = array();

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

    foreach($exemplares as $exemplar) {
      $situacaoExemplar = $this->getSituacaoForExemplar($exemplar);

      $nomeClienteReserva    = '';
      $dataReserva           = '';
      $dataDevolucaoPrevista = '';

      if ($situacaoExemplar['flag'] == 'emprestado') {
        $reserva = $this->getReservaForExemplar($exemplar);

        if(is_array($reserva['cliente']))
          $nomeClienteReserva = $reserva['cliente']['id'] . ' - ' . $reserva['cliente']['nome'];

        $dataReserva           = $reserva['dataReserva'];
        $dataDevolucaoPrevista = $reserva['dataDevolucaoPrevista'];
      }


      else
        $nomeClienteReserva = '';

      $e = array('id'                      => $exemplar['cod_exemplar'],
                 'situacao'                => $situacaoExemplar,
                 'cliente_reserva'         => $nomeClienteReserva,
                 'data_reserva'            => $dataReserva,
                 'data_devolucao_prevista' => $dataDevolucaoPrevista
      );

      $_exemplares[] = $e;
    }

    return $_exemplares;
  }


  protected function loadSituacaoById($id) {
    $situacao = new clsPmieducarSituacao($id);
    return $situacao->detalhe();
  }


  protected function getSituacaoForExemplar($exemplar) {
    $situacoes = array(
      'indisponivel' => array('flag' => 'indisponivel', 'label' => 'Indisponível'),
      'disponivel'   => array('flag' => 'disponivel'  , 'label' => 'Disponível'  ),
      'emprestado'   => array('flag' => 'emprestado'  , 'label' => 'Emprestado'  ),
      'invalida'     => array('flag' => 'invalida'    , 'label' => 'Inválida'  ),
    );

    $flagPermiteEmprestimo = 2;
    $situacaoCadastro = $this->loadSituacaoById($exemplar["ref_cod_situacao"]);

    if ($situacaoCadastro["situacao_emprestada"] == 1)
      $situacao = $situacoes['emprestado'];

    elseif($situacaoCadastro["permite_emprestimo"] == $flagPermiteEmprestimo &&
           $exemplar["permite_emprestimo"] == $flagPermiteEmprestimo) {
      $situacao = $situacoes['disponivel'];
    }

    elseif($situacaoCadastro["permite_emprestimo"] != $flagPermiteEmprestimo ||
           $exemplar["permite_emprestimo"] != $flagPermiteEmprestimo) {
      $situacao = $situacoes['indisponivel'];
    }

    else
      $situacao = $situacoes['invalida'];

    return $situacao;
  }


  protected function getReservaForExemplar($exemplar) {
    $_reserva = array('cliente'              => null,
                     'dataReserva'           => '#TODO',
                     'dataDevolucaoPrevista' => '#TODO'
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

		if(is_array($reserva) && ! empty($reserva))
		{
			$reserva = array_shift($reserva);
      $_reserva['dataReserva']           = date('d/m/Y', strtotime($reserva['data_reserva']));
      $_reserva['dataDevolucaoPrevista'] = date('d/m/Y', strtotime($reserva['data_prevista_disponivel']));
      $_reserva['cliente']               = $this->getCliente($reserva["ref_cod_cliente"]);
    }

    return $_reserva;
  }


  protected function getCliente($clienteId = '') {

    if (empty($clienteId))
      $clienteId = $this->getRequest()->ref_cod_cliente;

    $_cliente = array('id' => $clienteId);

		$cliente = new clsPmieducarCliente($clienteId);
		$cliente = $cliente->detalhe();

    $_cliente['pessoaId'] = $cliente["ref_idpes"];

		$pessoa = new clsPessoa_($_cliente['pessoaId']);
		$pessoa = $pessoa->detalhe();

    $_cliente['nome'] = $pessoa["nome"];

    $sql = "select 1 from pmieducar.cliente_suspensao where ref_cod_cliente = $1 and data_liberacao is null and data_suspensao + (dias||' day')::interval >= now()";
    $suspenso = $this->fetchPreparedQuery($sql, $params = array($clienteId), true, 'first-field');

    $_cliente['suspenso'] = $suspenso == '1';

    return $_cliente;
  }


  protected function getSituacaoExemplar($exemplarId) {
    return "#TODO";
  }


  protected function validatesSituacaoExemplarIs($situacao) {
    // TODO add msg if false
    $this->messenger->append("Situação do exemplar deve ser '$situacao'", 'error');
    return false;
  }


  protected function validatesClienteIsNotSuspenso() {
    $cliente = $this->getCliente();

    if($cliente['suspenso']) {
      $this->messenger->append("O cliente esta suspenso", 'error');
      return false;
    }

    return true;
  }


  protected function canPostReserva() {
    return $this->validatesClienteIsNotSuspenso() &&
           $this->validatesPresenceOfExemplarId() &&
           $this->validatesSituacaoExemplarIs('disponivel');
  }


  protected function postReserva() {
    if ($this->canPostReserva()) {


      // TODO pegar excessoes no post, se pegar add msg erro inesperado


      $situacaoExemplar = $this->getSituacaoExemplar($exemplarId);

      $this->appendResponse('situacao_exemplar',     $situacaoExemplar);
      $this->appendResponse('cliente',               '#TODO');
      $this->appendResponse('dataReserva',           '#TODO');
      $this->appendResponse('dataDevolucaoPrevista', '#TODO');
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
