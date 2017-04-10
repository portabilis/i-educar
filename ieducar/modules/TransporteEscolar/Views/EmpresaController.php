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

class EmpresaController extends Portabilis_Controller_Page_EditController
{
  protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
  protected $_titulo     = 'i-Educar - Empresas';

  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_processoAp        = 21235;
  protected $_deleteOption      = true;

  protected $_formMap    = array(
    'pessoa' => array(
      'label'  => 'Pessoa responsável',
      'help'   => '',
    ),

    'observacao' => array(
      'label'  => 'Observações',
      'help'   => '',
    ),

    'id' => array(
      'label'  => 'Código da empresa',
      'help'   => '',
    ),

    'pessoaj' => array(
      'label'  => 'Pessoa jurídica',
      'help'   => '',
    )
  );


  protected function _preConstruct()
  {
    $this->_options = $this->mergeOptions(array('edit_success' => '/intranet/transporte_empresa_lst.php','delete_success' => '/intranet/transporte_empresa_lst.php'), $this->_options);
    $nomeMenu = $this->getRequest()->id == null ? "Cadastrar" : "Editar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_transporte_escolar_index.php"                  => "Transporte escolar",
         ""        => "$nomeMenu empresa"             
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
    $this->url_cancelar = '/intranet/transporte_empresa_lst.php';

    // Código da empresa
    $options = array('label'    => $this->_getLabel('id'), 'disabled' => true,
                     'required' => false, 'size' => 25);
    $this->inputsHelper()->integer('id', $options);


    $options = array('label' => $this->_getLabel('pessoaj'), 'required' => true);
//    $this->inputsHelper()->integer('pessoaj', $options);
    $this->inputsHelper()->simpleSearchPessoaj('pessoaj', $options);

       // nome
    $options = array('label' => $this->_getLabel('pessoa'), 'size' => 68);
    $this->inputsHelper()->simpleSearchPessoa('nome', $options);


    // observações
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('observacao')), 'required' => false, 'size' => 50, 'max_length' => 253);
    $this->inputsHelper()->textArea('observacao', $options);
    
    $this->loadResourceAssets($this->getDispatcher());
  }

}
?>