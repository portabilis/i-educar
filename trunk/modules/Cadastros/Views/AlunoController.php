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

require_once 'lib/Portabilis/Controller/Page/EditController.php';
require_once 'Usuario/Model/FuncionarioDataMapper.php';

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

    'inep_id' => array(
      'label'  => 'C&oacutedigo inep',
      'help'   => '',
    ),

    'deficiencias' => array(
      'label'  => 'Deficiências / habilidades especiais',
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

    // código rede de ensino municipal
    $options = array('label'    => $this->_getLabel('id'), 'disabled' => true,
                     'required' => false, 'size' => 25);
    $this->inputsHelper()->text('id', $options);


    // código inep
    $options = array('label' => $this->_getLabel('inep_id'), 'required' => false, 'size' => 25);
    $this->inputsHelper()->text('inep_id', $options);


    // nome
    $options = array('label' => $this->_getLabel('pessoa'), 'size' => 68);
    $this->inputsHelper()->simpleSearchPessoa('nome', $options);

    // rg
    //$options = array('label' => $this->_getLabel('rg'), 'disabled' => true, 'required' => false);
    //$this->inputsHelper()->text('aluno', 'rg', $options);

    // cpf
    //$options = array('label' => $this->_getLabel('cpf'), 'disabled' => true, 'required' => false);
    //$this->inputsHelper()->text('aluno', 'cpf', $options);

    // pai
    $options = array('label' => $this->_getLabel('pai'), 'disabled' => true, 'required' => false, 'size' => 68);
    $this->inputsHelper()->text('pai', $options);


    // mãe
    $options = array('label' => $this->_getLabel('mae'), 'disabled' => true, 'required' => false, 'size' => 68);
    $this->inputsHelper()->text('mae', $options);


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

    $this->inputsHelper()->select('tipo_responsavel', $options);


    // nome
    $helperOptions = array('objectName' => 'responsavel');
    $options       = array('label' => '', 'size' => 50, 'required' => true);

    $this->inputsHelper()->simpleSearchPessoa('nome', $options, $helperOptions);


    // transporte publico
    $tiposTransporte = array(null        => 'Selecione',
                             'nenhum'    => 'N&atilde;o utiliza',
                             'municipal' => 'Municipal',
                             'estadual'  => 'Estadual');

    $options = array('label'     => $this->_getLabel('transporte'),
                     'resources' => $tiposTransporte,
                     'required'  => true);

    $this->inputsHelper()->select('tipo_transporte', $options);


    // religião
    $this->inputsHelper()->religiao(array('required' => false));

    // beneficio
    $this->inputsHelper()->beneficio(array('required' => false));


    // alfabetizado
    $options = array('label' => $this->_getLabel('alfabetizado'));
    $this->inputsHelper()->checkbox('alfabetizado', $options);


    // Deficiências / habilidades especiais
    $helperOptions = array('objectName' => 'deficiencias');
    $options       = array('label' => $this->_getLabel('deficiencias'), 'size' => 50, 'required' => false);

    $this->inputsHelper()->multipleSearchDeficiencias('', $options, $helperOptions);


    $this->loadResourceAssets($this->getDispatcher());
  }
}
?>