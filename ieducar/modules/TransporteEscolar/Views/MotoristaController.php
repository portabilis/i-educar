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
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   TransporteEscolar
 * @subpackage  Modules
 * @since     Arquivo disponível desde a versão ?
 * @version   $Id$
 */
require_once 'lib/Portabilis/Controller/Page/EditController.php';
require_once 'Usuario/Model/FuncionarioDataMapper.php';

class MotoristaController extends Portabilis_Controller_Page_EditController
{
  protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
  protected $_titulo     = 'i-Educar - Motoristas';

  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_processoAp        = 21236;
  protected $_deleteOption      = true;

  protected $_formMap    = array(
    'id' => array(
      'label'  => 'Código do motorista',
      'help'   => '',
    ),

    'pessoa' => array(
      'label'  => 'Pessoa',
      'help'   => '',
    ),

    'cnh' => array(
      'label'  => 'CNH',
      'help'   => '',
    ),

    'tipo_cnh' =>array(
      'label'  => 'Categoria CNH',
      'help'   => '',
    ),

    'dt_habilitacao' =>array(
      'label'  => 'Data da habilitação',
      'help'   => '',
    ),

    'vencimento_cnh' =>array(
      'label'  => 'Vencimento da habilitação',
      'help'   => '',
    ),

    'ref_cod_empresa_transporte_escolar' =>array(
      'label'  => 'Empresa',
      'help'   => '',
    ),

    'observacao' =>array(
      'label'  => 'Observações',
      'help'   => '',
    )

  );


  protected function _preConstruct()
  {
    $this->_options = $this->mergeOptions(array('edit_success' => '/intranet/transporte_motorista_lst.php','delete_success' => '/intranet/transporte_motorista_lst.php'), $this->_options);
    $nomeMenu = $this->getRequest()->id == null ? "Cadastrar" : "Editar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_transporte_escolar_index.php"                  => "Transporte escolar",
         ""        => "$nomeMenu motorista"             
    ));
    $this->enviaLocalizacao($localizacao->montar());    
  }


  protected function _initNovo() {
    return false;
  }


  protected function _initEditar() {
    return false;
  }


  public function Gerar()
  {
    $this->url_cancelar = '/intranet/transporte_motorista_lst.php';

    // Código do Motorista
    $options = array('label'    => $this->_getLabel('id'), 'disabled' => true,
                     'required' => false, 'size' => 25);
    $this->inputsHelper()->integer('id', $options);

    // nome
    $options = array('label' => $this->_getLabel('pessoa'), 'size' => 50);
    $this->inputsHelper()->simpleSearchPessoa('nome', $options);

    //número da CNH
    $options = array('label' => $this->_getLabel('cnh'), 'max_length' => 15, 'size' => 15, 'placeholder' => Portabilis_String_Utils::toLatin1('Número da CNH'), 'required' => false);
    $this->inputsHelper()->integer('cnh',$options);

    //Categoria da CNH
    $options = array('label' => $this->_getLabel('tipo_cnh'), 'max_length' => 2, 'size' => 1, 'placeholder' => '', 'required' => false);
    $this->inputsHelper()->text('tipo_cnh',$options);    

    // Vencimento
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('dt_habilitacao')), 'required' => false, 'size' => 10, 'placeholder' => '');
    $this->inputsHelper()->date('dt_habilitacao',$options);

    // Habilitação
    $options = array('label' =>Portabilis_String_Utils::toLatin1($this->_getLabel('vencimento_cnh')), 'required' => false, 'size' => 10,'placeholder' => '');
    $this->inputsHelper()->date('vencimento_cnh',$options);  

    // Codigo da empresa
    $options       = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('ref_cod_empresa_transporte_escolar')), 'required'   => true);  
    $this->inputsHelper()->simpleSearchEmpresa('ref_cod_empresa_transporte_escolarf', $options);

    // observações    
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('observacao')), 'required' => false, 'size' => 50, 'max_length' => 255);
    $this->inputsHelper()->textArea('observacao', $options);

    $this->loadResourceAssets($this->getDispatcher());
  }

}
?>