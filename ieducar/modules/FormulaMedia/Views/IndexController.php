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
 * @version     $Id: /ieducar/branches/1.1.0-avaliacao/ieducar/modules/AreaConhecimento/Views/IndexController.php 791 2009-11-27T16:23:16.505103Z eriksen  $
 */

require_once 'Core/Controller/Page/ListController.php';
require_once 'FormulaMedia/Model/FormulaDataMapper.php';

/**
 * IndexController class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     FormulaMedia
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class IndexController extends Core_Controller_Page_ListController
{
  protected $_dataMapper = 'FormulaMedia_Model_FormulaDataMapper';
  protected $_titulo     = 'Listagem de fórmulas de cálculo de média';
  protected $_processoAp = 948;
  protected $_tableMap   = array(
    'Nome' => 'nome',
    'Fórmula de cálculo' => 'formulaMedia',
    'Tipo fórmula' => 'tipoFormula'
  );

  protected function _preRender(){

    parent::_preRender();

    $localizacao = new LocalizacaoSistema();

    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Escola",
         ""                                  => "Listagem de f&oacute;rmulas de m&eacute;dia"
    ));
    $this->enviaLocalizacao($localizacao->montar());
  }
}
