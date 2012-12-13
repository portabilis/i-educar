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

    'cpf' => array(
      'label'  => 'CPF',
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

    'religiao' => array(
      'label'  => 'Religi&atilde;o',
      'help'   => '',
    ),

    'beneficio' => array(
      'label'  => 'Beneficio',
      'help'   => '',
    ),

    'alfabetizado' => array(
      'label'  => 'Alfabetizado',
      'help'   => '',
    ),

    'transporte' => array(
      'label'  => 'Transporte p&uacute;blico',
      'help'   => '',
    ),

    'id' => array(
      'label'  => 'C&oacutedigo aluno',
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
    $helperOptions = array('addHiddenInput' => true);
    $options       = array('label'          => $this->_getLabel('pessoa'), 'size' => 68);
    $this->inputsHelper()->simpleSearchInput('pessoa', 'nome', $options, $helperOptions);

    // rg
    //$options = array('label' => $this->_getLabel('rg'), 'disabled' => true, 'required' => false);
    //$this->inputsHelper()->textInput('aluno', 'rg', $options);

    // cpf
    //$options = array('label' => $this->_getLabel('cpf'), 'disabled' => true, 'required' => false);
    //$this->inputsHelper()->textInput('aluno', 'cpf', $options);

    // pai
    $options = array('label' => $this->_getLabel('pai'), 'disabled' => true, 'required' => false, 'size' => 68);
    $this->inputsHelper()->textInput('pai', $options);


    // mãe
    $options = array('label' => $this->_getLabel('mae'), 'disabled' => true, 'required' => false, 'size' => 68);
    $this->inputsHelper()->textInput('mae', $options);


    // responsável

    // tipo
    $tiposResponsavel = array(null           => 'Selecione',
                              'pai'          => 'Pai',
                              'mae'          => 'M&atilde;e',
                              'outra_pessoa' => 'Outra pessoa');

    $options = array('label'     => $this->_getLabel('responsavel'),
                     'resources' => $tiposResponsavel,
                     'required'  => true,
                     'inline'    => true);

    $this->inputsHelper()->selectInput('tipo_responsavel', $options);


    // nome
    $options       = array('label'          => '', 'size' => 50, 'required' => true);
    $helperOptions = array('addHiddenInput' => true,
                           'searchPath'     => "/module/Api/Pessoa?oper=get&resource=pessoa-search");

    $this->inputsHelper()->simpleSearchInput('responsavel', 'nome', $options, $helperOptions);


    // religião
    $options = array('label' => $this->_getLabel('religiao'), 'required' => false);
    $this->inputsHelper()->religiaoInput($options);


    // beneficio
    $options = array('label' => $this->_getLabel('beneficio'), 'required' => false);
    $this->inputsHelper()->beneficioInput($options);


    // transporte publico
    $tiposTransporte = array(null => 'Selecione',
                             'nenhum'    => 'N&atilde;o utiliza',
                             'municipal' => 'Municipal',
                             'estadual'  => 'Estadual');

    $options = array('label'     => $this->_getLabel('transporte'),
                     'resources' => $tiposTransporte,
                     'required'  => true);

    $this->inputsHelper()->selectInput('tipo_transporte', $options);


    // alfabetizado
    $options = array('label' => $this->_getLabel('alfabetizado'));
    $this->inputsHelper()->checkboxInput('alfabetizado', $options);


    // código rede de ensino municipal
    $options = array('label'    => $this->_getLabel('id'), 'disabled' => true,
                     'required' => false, 'size' => 25);
    $this->inputsHelper()->textInput('id', $options);


    // código inep
    $options = array('label' => $this->_getLabel('codigo_inep'), 'required' => false, 'size' => 25);
    $this->inputsHelper()->textInput('codigo_inep', $options);

    $this->loadResourceAssets();
    Portabilis_View_Helper_Application::loadJavascript($this, '/modules/Cadastros/Assets/Javascripts/Aluno.js');
  }
}
?>