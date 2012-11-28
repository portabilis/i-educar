<?php

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

require_once 'lib/Portabilis/Controller/Page/EditController.php';
require_once 'Usuario/Model/FuncionarioDataMapper.php';
require_once 'Usuario/Mailers/UsuarioMailer.php';

#require_once "lib/Portabilis/View/Helper/Inputs.php";

class AlunoController extends Portabilis_Controller_Page_EditController
{
  protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
  protected $_titulo     = 'Cadastro de aluno';
  protected $_processoAp = 0;

  protected $_formMap    = array(
    'pessoa' => array(
      'label'  => 'Pessoa',
      'help'   => '',
    ),

    'rg' => array(
      'label'  => 'Documento de identidade (rg)',
      'help'   => '',
    ),

    'pai' => array(
      'label'  => 'Pai',
      'help'   => '',
    ),

    'mae' => array(
      'label'  => 'M&atilde;e',
      'help'   => '',
    ),

    'responsavel' => array(
      'label'  => 'Respons&aacute;vel',
      'help'   => '',
    ),

    'transporte_publico' => array(
      'label'  => 'Transporte p&uacute;blico',
      'help'   => '',
    ),

    'codigo_rede_ensino_municipal' => array(
      'label'  => 'C&oacutedigo rede de ensino municipal',
      'help'   => '',
    ),

    'codigo_rede_ensino_estadual' => array(
      'label'  => 'C&oacutedigo rede de ensino estadual',
      'help'   => '',
    ),

    'codigo_inep' => array(
      'label'  => 'C&oacutedigo inep',
      'help'   => '',
    )
  );


  protected function _preConstruct()
  {
  }


  protected function _initNovo() {
    return false;
  }


  protected function _initEditar() {
    return false;
  }


  public function Gerar()
  {
    //$this->campoRotulo('pessoa', $this->_getLabel('pessoa'), '');
    $this->url_cancelar = '/intranet/educar_aluno_lst.php';


    // nome
    //$this->inputsHelper()->textInput('pessoa', 'nome', $options);
    //$this->inputsHelper()->hiddenInput('pessoa', 'id');

    //$helperOptions = array('searchPath' => 'intranet/educar_pesquisa_aluno_lst2.php?campo1=campo_1&campo2=campo_2&campo3=campo_3&campo4=campo_4');
    //$this->inputsHelper()->searchInput('pessoa', 'id', $options, $helperOptions);

    //$helperOptions = array('searchPath' => "module/Api/Pessoa&resource=pessoa-search&query=");
    //$helperOptions = array('targetElement' => "");

    $options       = array('label' => $this->_getLabel('pessoa'));

    $helperOptions = array('addHiddenInput' => true);
    $this->inputsHelper()->simpleSearchInput('pessoa', 'nome', $options, $helperOptions);

    // rg
    $options = array('label' => $this->_getLabel('rg'), 'disabled' => true);
    $this->inputsHelper()->textInput('aluno', 'rg', $options);


    // pai
    $options = array('label' => $this->_getLabel('pai'), 'disabled' => true);
    $this->inputsHelper()->textInput('aluno', 'pai', $options);


    // mãe
    $options = array('label' => $this->_getLabel('mae'), 'disabled' => true);
    $this->inputsHelper()->textInput('aluno', 'mae', $options);


    // responsável
    $options = array('label' => $this->_getLabel('responsavel'), 'disabled' => true);
    $this->inputsHelper()->textInput('aluno', 'responsavel', $options);


    // transporte publico
    $tiposTransportePublico = array('nenhum'    => 'N&atilde;o utiliza',
                                    'municipal' => 'Municipal',
                                    'estadual'  => 'Estadual');

    $options = array('label'     => $this->_getLabel('transporte_publico'),
                     'resources' => $tiposTransportePublico);

    $this->inputsHelper()->selectInput('aluno', 'transporte_publico', $options);


    // código rede de ensino municipal
    $options = array('label' => $this->_getLabel('codigo_rede_ensino_municipal'), 'disabled' => true);
    $this->inputsHelper()->textInput('aluno', 'codigo_rede_ensino_municipal', $options);


    // código rede de ensino estadual
    $options = array('label' => $this->_getLabel('codigo_rede_ensino_estadual'));
    $this->inputsHelper()->textInput('aluno', 'codigo_rede_ensino_estadual', $options);


    // código inep
    $options = array('label' => $this->_getLabel('codigo_inep'));
    $this->inputsHelper()->textInput('aluno', 'codigo_inep', $options);
  }
}
?>