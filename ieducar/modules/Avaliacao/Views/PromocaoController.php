<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *           <ctima@itajai.sc.gov.br>
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
 * @package   Avaliacao
 * @subpackage  Modules
 * @since     Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'Portabilis/Controller/Page/ListController.php';
require_once 'lib/Portabilis/View/Helper/Application.php';
require_once 'lib/Portabilis/View/Helper/Inputs.php';

// TODO migrar para novo padrao

class PromocaoController extends Portabilis_Controller_Page_ListController
{
  protected $_dataMapper = 'Avaliacao_Model_NotaAlunoDataMapper';
  protected $_titulo     = 'Lan&ccedil;amento por turma';
  protected $_processoAp = 644;
  protected $_formMap    = array();

  public function Gerar() {
    $this->inputsHelper()->dynamic('ano', array('id' => 'ano'));
    $this->inputsHelper()->dynamic('instituicao', array('id' => 'instituicao_id'));
    $this->inputsHelper()->dynamic('escola', array('id' => 'escola', 'required' => false));
    $this->inputsHelper()->dynamic('curso', array('id' => 'curso', 'required' => false));
    $this->inputsHelper()->dynamic('serie', array('id' => 'serie', 'required' => false));
    $this->inputsHelper()->dynamic('turma', array('id' => 'turma', 'required' => false));
    $this->inputsHelper()->dynamic('situacaoMatricula', array('id' => 'matricula', 'value' => 10, 'required' => false));

    $this->loadResourceAssets($this->getDispatcher());

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_configuracoes_index.php"    => "Configurações",
         ""                                  => "Atualização de matrículas"
    ));
    $this->enviaLocalizacao($localizacao->montar());
  }
}
?>
