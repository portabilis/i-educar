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
 * @package     FormulaMedia
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'Core/Controller/Page/EditController.php';
require_once 'FormulaMedia/Model/FormulaDataMapper.php';
require_once 'FormulaMedia/Validate/Formula.php';

/**
 * EditController class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     FormulaMedia
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class EditController extends Core_Controller_Page_EditController
{
  protected $_dataMapper        = 'FormulaMedia_Model_FormulaDataMapper';
  protected $_titulo            = 'Cadastro de fórmula de cálculo de média';
  protected $_processoAp        = 948;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::INSTITUCIONAL;
  protected $_saveOption        = TRUE;
  protected $_deleteOption      = TRUE;

  protected $_formMap = array(
    'instituicao' => array(
      'label'  => 'Instituição',
      'help'   => ''
    ),
    'nome' => array(
      'label'  => 'Nome',
      'help'   => ''
    ),
    'formulaMedia' => array(
      'label'  => 'Fórmula de média final',
      'help'   => 'A fórmula de cálculo.<br />
                   Variáveis disponíveis:<br />
                   &middot; En - Etapa n (de 1 a 10)<br />
                   &middot; Et - Total de etapas<br />
                   &middot; Se - Soma das notas das etapas<br />
                   &middot; Rc - Nota da recuperação<br />
                   Símbolos disponíveis:<br />
                   &middot; (), +, /, *, x<br />
                   A variável "Rc" está disponível apenas<br />
                   quando Tipo de fórmula for "Recuperação".'
    ),
    'tipoFormula' => array(
      'label'  => 'Tipo de fórmula',
      'help'   => ''
    )
  );

  /**
   * @see clsCadastro#Gerar()
   */
  public function Gerar()
  {
    $this->campoOculto('id', $this->getEntity()->id);

    // Instituição
    $instituicoes = App_Model_IedFinder::getInstituicoes();
    $this->campoLista('instituicao', $this->_getLabel('instituicao'),
      $instituicoes, $this->getEntity()->instituicao);

    // Nome
    $this->campoTexto('nome', $this->_getLabel('nome'), $this->getEntity()->nome,
      40, 50, TRUE, FALSE, FALSE, $this->_getHelp('nome'));

    // Fórmula de média
    $this->campoTexto('formulaMedia', $this->_getLabel('formulaMedia'),
      $this->getEntity()->formulaMedia, 40, 50, TRUE, FALSE, FALSE,
      $this->_getHelp('formulaMedia'));

    // Fórmula de recuperação
    /*$this->campoTexto('formulaRecuperacao', $this->_getLabel('formulaRecuperacao'),
      $this->getEntity()->formulaRecuperacao, 40, 50, TRUE, FALSE, FALSE,
      $this->_getHelp('formulaRecuperacao'));*/

    // Tipo de fórmula
    $tipoFormula = FormulaMedia_Model_TipoFormula::getInstance();
    $this->campoRadio('tipoFormula', $this->_getLabel('tipoFormula'),
      $tipoFormula->getEnums(), $this->getEntity()->get('tipoFormula'));
  }
}