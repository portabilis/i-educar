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

  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_processoAp        = 578;
  protected $_deleteOption      = true;

  protected $_formMap    = array(
    'pessoa' => array(
      'label'  => 'Pessoa',
      'help'   => '',
    ),

    'rg' => array(
      'label'  => 'Documento de identidade (RG)',
      'help'   => '',
    ),

    'pai' => array(
      'label'  => 'Pai',
      'help'   => '',
    ),

    'mae' => array(
      'label'  => 'Mãe',
      'help'   => '',
    ),

    'responsavel' => array(
      'label'  => 'Responsável',
      'help'   => '',
    ),

    'alfabetizado' => array(
      'label'  => 'Alfabetizado',
      'help'   => '',
    ),

    'transporte' => array(
      'label'  => 'Transporte público',
      'help'   => '',
    ),

    'id' => array(
      'label'  => 'Código aluno',
      'help'   => '',
    ),

    'aluno_inep_id' => array(
      'label'  => 'Código inep',
      'help'   => '',
    ),

    'aluno_estado_id' => array(
      'label'  => 'Código rede estadual',
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
    $this->url_cancelar = '/intranet/educar_aluno_lst.php';

    // código aluno
    $options = array('label'    => $this->_getLabel('id'), 'disabled' => true,
                     'required' => false, 'size' => 25);
    $this->inputsHelper()->integer('id', $options);


    // código aluno inep
    $options = array('label' => $this->_getLabel('aluno_inep_id'), 'required' => false, 'size' => 25, 'max_length' => 14);
    $this->inputsHelper()->integer('aluno_inep_id', $options);

    // código aluno rede estadual
    $options = array('label' => $this->_getLabel('aluno_estado_id'), 'required' => false, 'size' => 25, 'max_length' => 25);
    $this->inputsHelper()->text('aluno_estado_id', $options);

    // nome
    $options = array('label' => $this->_getLabel('pessoa'), 'size' => 68);
    $this->inputsHelper()->simpleSearchPessoa('nome', $options);

    // data nascimento
    $options = array('label' => 'Data nascimento', 'disabled' => true, 'required' => false, 'size' => 25, 'placeholder' => '');
    $this->inputsHelper()->date('data_nascimento', $options);

    // rg
    $options = array('label' => $this->_getLabel('rg'), 'disabled' => true, 'required' => false, 'size' => 25);
    $this->inputsHelper()->integer('rg', $options);

    // pai
    $options = array('label' => $this->_getLabel('pai'), 'disabled' => true, 'required' => false, 'size' => 68);
    $this->inputsHelper()->text('pai', $options);


    // mãe
    $options = array('label' => $this->_getLabel('mae'), 'disabled' => true, 'required' => false, 'size' => 68);
    $this->inputsHelper()->text('mae', $options);


    // responsável

    // tipo

    $label = Portabilis_String_Utils::toLatin1($this->_getLabel('responsavel'));

    $tiposResponsavel = array(null           => $label,
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

    $label = Portabilis_String_Utils::toLatin1($this->_getLabel('transporte'));

    $tiposTransporte = array(null        => $label,
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


    // Deficiências / habilidades especiais
    $helperOptions = array('objectName' => 'deficiencias');
    $options       = array('label' => $this->_getLabel('deficiencias'), 'size' => 50, 'required' => false,
                           'options' => array('value' => null));

    $this->inputsHelper()->multipleSearchDeficiencias('', $options, $helperOptions);


    // alfabetizado
    $options = array('label' => $this->_getLabel('alfabetizado'), 'value' => 'checked');
    $this->inputsHelper()->checkbox('alfabetizado', $options);


    $this->loadResourceAssets($this->getDispatcher());
  }
}
?>