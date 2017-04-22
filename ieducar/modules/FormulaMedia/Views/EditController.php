<?php

/**
 * i-Educar - Sistema de gestדo escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaם
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa י software livre; vocך pode redistribuם-lo e/ou modificב-lo
 * sob os termos da Licenחa Pתblica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versדo 2 da Licenחa, como (a seu critיrio)
 * qualquer versדo posterior.
 *
 * Este programa י distribuם­do na expectativa de que seja תtil, porיm, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implם­cita de COMERCIABILIDADE OU
 * ADEQUAֳַO A UMA FINALIDADE ESPECֽFICA. Consulte a Licenחa Pתblica Geral
 * do GNU para mais detalhes.
 *
 * Vocך deve ter recebido uma cףpia da Licenחa Pתblica Geral do GNU junto
 * com este programa; se nדo, escreva para a Free Software Foundation, Inc., no
 * endereחo 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Eriksen Costa Paixדo <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     FormulaMedia
 * @subpackage  Modules
 * @since       Arquivo disponםvel desde a versדo 1.1.0
 * @version     $Id$
 */

require_once 'Core/Controller/Page/EditController.php';
require_once 'FormulaMedia/Model/FormulaDataMapper.php';
require_once 'FormulaMedia/Validate/Formula.php';

/**
 * EditController class.
 *
 * @author      Eriksen Costa Paixדo <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     FormulaMedia
 * @subpackage  Modules
 * @since       Classe disponםvel desde a versדo 1.1.0
 * @version     @@package_version@@
 */
class EditController extends Core_Controller_Page_EditController
{
  protected $_dataMapper        = 'FormulaMedia_Model_FormulaDataMapper';
  protected $_titulo            = 'Cadastro de fףrmula de cבlculo de mיdia';
  protected $_processoAp        = 948;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::INSTITUCIONAL;
  protected $_saveOption        = TRUE;
  protected $_deleteOption      = TRUE;

  protected $_formMap = array(
    'instituicao' => array(
      'label'  => 'Instituiחדo',
      'help'   => ''
    ),
    'nome' => array(
      'label'  => 'Nome',
      'help'   => ''
    ),
    'formulaMedia' => array(
      'label'  => 'Fףrmula de mיdia final',
      'help'   => 'A fףrmula de cבlculo.<br />
                   Variבveis disponםveis:<br />
                   &middot; En - Etapa n (de 1 a 10)<br />
                   &middot; Et - Total de etapas<br />
                   &middot; Se - Soma das notas das etapas<br />
                   &middot; Rc - Nota da recuperaחדo<br />
                   Sםmbolos disponםveis:<br />
                   &middot; (), +, /, *, x<br />
                   A variבvel "Rc" estב disponםvel apenas<br />
                   quando Tipo de fףrmula for "Recuperaחדo".'
    ),
    'tipoFormula' => array(
      'label'  => 'Tipo de fףrmula',
      'help'   => ''
    )
  );

  function _preRender(){
    Portabilis_View_Helper_Application::loadStylesheet($this, 'intranet/styles/localizacaoSistema.css');

    $nomeMenu = $this->getRequest()->id == null ? "Cadastrar" : "Editar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "i-Educar - Escola",
         ""        => "$nomeMenu f&oacute;rmula de m&eacute;dia"
    ));
    $this->enviaLocalizacao($localizacao->montar());
  }

  /**
   * @see clsCadastro#Gerar()
   */
  public function Gerar()
  {
    $this->campoOculto('id', $this->getEntity()->id);

    // Instituiחדo
    $instituicoes = App_Model_IedFinder::getInstituicoes();
    $this->campoLista('instituicao', $this->_getLabel('instituicao'),
      $instituicoes, $this->getEntity()->instituicao);

    // Nome
    $this->campoTexto('nome', $this->_getLabel('nome'), $this->getEntity()->nome,
      40, 50, TRUE, FALSE, FALSE, $this->_getHelp('nome'));

    // Fףrmula de mיdia
    $this->campoTexto('formulaMedia', $this->_getLabel('formulaMedia'),
      $this->getEntity()->formulaMedia, 40, 50, TRUE, FALSE, FALSE,
      $this->_getHelp('formulaMedia'));

    // Fףrmula de recuperaחדo
    /*$this->campoTexto('formulaRecuperacao', $this->_getLabel('formulaRecuperacao'),
      $this->getEntity()->formulaRecuperacao, 40, 50, TRUE, FALSE, FALSE,
      $this->_getHelp('formulaRecuperacao'));*/

    // Tipo de fףrmula
    $tipoFormula = FormulaMedia_Model_TipoFormula::getInstance();
    $this->campoRadio('tipoFormula', $this->_getLabel('tipoFormula'),
      $tipoFormula->getEnums(), $this->getEntity()->get('tipoFormula'));
  }
}
