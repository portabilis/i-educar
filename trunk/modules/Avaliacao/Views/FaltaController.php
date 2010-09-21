<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
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
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'Core/Controller/Page/EditController.php';
require_once 'Avaliacao/Model/NotaComponenteDataMapper.php';
require_once 'Avaliacao/Service/Boletim.php';

/**
 * FaltaController class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class FaltaController extends Core_Controller_Page_EditController
{
  protected $_dataMapper        = 'Avaliacao_Model_NotaComponenteDataMapper';
  protected $_titulo            = 'Avaliação do aluno | Falta';
  protected $_processoAp        = 642;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_saveOption        = TRUE;
  protected $_deleteOption      = FALSE;

  /**
   * @var Avaliacao_Service_Boletim
   */
  protected $_service = NULL;

  /**
   * @var RegraAvaliacao_Model_Regra
   */
  protected $_regra = NULL;

  /**
   * @var int
   */
  protected $_matricula = NULL;

  /**
   * @var string
   */
  protected $_etapa = NULL;

  /**
   * @var Avaliacao_Model_NotaComponente
   */
  protected $_nota = NULL;

  /**
   * @var Avaliacao_Model_FaltaAbstract
   */
  protected $_falta = NULL;

  /**
   * @see Core_Controller_Page_EditController#_preConstruct()
   */
  protected function _preConstruct()
  {
    // Id do usuário na session
    $usuario = $this->getSession()->id_pessoa;

    $this->_options = array(
      'new_success'         => 'boletim',
      'new_success_params' => array('matricula' => $this->getRequest()->matricula),
      'edit_success'        => 'boletim',
      'edit_success_params' => array('matricula' => $this->getRequest()->matricula),
    );

    $this->_service = new Avaliacao_Service_Boletim(array(
      'matricula' => $this->getRequest()->matricula,
      'usuario' => $usuario
    ));

    $this->_regra = $this->_service->getRegra();
  }

  /**
   * @see Core_Controller_Page_EditController#_initNovo()
   */
  protected function _initNovo()
  {
    $this->_etapa = $this->getRequest()->etapa;
    $this->_matricula = $this->getRequest()->matricula;

    if (isset($this->_etapa) && isset($this->_matricula)) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * @see Core_Controller_Page_EditController#_initEditar()
   */
  protected function _initEditar()
  {
    $this->_falta = $this->_service->getFalta($this->_etapa);
    return TRUE;
  }

  /**
   * @see clsCadastro#Gerar()
   */
  public function Gerar()
  {
    $this->campoOculto('matricula', $this->_matricula);
    $this->campoOculto('etapa', $this->_etapa);

    $matricula = $this->_service->getOption('matriculaData');

    $this->campoRotulo('1nome', 'Nome', $matricula['nome']);
    $this->campoRotulo('2curso', 'Curso', $matricula['curso_nome']);
    $this->campoRotulo('3serie', 'Série', $matricula['serie_nome']);
    $this->campoRotulo('4turma', 'Turma', $matricula['turma_nome']);
    $this->campoRotulo('5etapa', 'Etapa', $this->_etapa == 'Rc' ? 'Recuperação' : $this->_etapa);

    $this->campoLista('falta', 'Falta', range(0, 100, 1), $this->_falta->quantidade);
  }

  /**
   * @see Core_Controller_Page_EditController#_save()
   */
  protected function _save()
  {
    $quantidade = 0 < $this->getRequest()->falta ?
      (int) $this->getRequest()->falta : 0;

    $falta = new Avaliacao_Model_FaltaGeral(array(
      'quantidade' => $quantidade,
      'etapa' => $this->getRequest()->etapa
    ));

    $this->_service->addFalta($falta);

    try {
      $this->_service->save();
    }
    catch (CoreExt_Service_Exception $e) {
      // Ok. Não pode promover por se tratar de progressão manual ou por estar em andamento
    }
    catch (Exception $e) {
      $this->mensagem = 'Erro no preenchimento do formulário. ';
      return FALSE;
    }

    return TRUE;
  }
}