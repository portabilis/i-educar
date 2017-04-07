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
require_once 'include/modules/clsModulesRotaTransporteEscolar.inc.php';
require_once ("include/clsBanco.inc.php");

class PessoatransporteController extends Portabilis_Controller_Page_EditController
{
  protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
  protected $_titulo     = 'i-Educar - Usu&aacute;rios de transporte';

  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_processoAp        = 21240;
  protected $_deleteOption      = true;

  protected $_formMap    = array(

    'id' => array(
      'label'  => 'Código',
      'help'   => '',
    ),
    'pessoa' => array(
      'label'  => 'Pessoa',
      'help'   => '',
    ),
    'rota' => array(
      'label'  => 'Rota',
      'help'   => '',
    ),
    'ponto' => array(
      'label'  => 'Ponto de embarque',
      'help'   => '',
    ),    
    'destino' => array(
      'label'  => 'Destino (Caso for diferente da rota)',
      'help'   => '',
    ),        
    'observacao' => array(
      'label'  => 'Observações',
      'help'   => '',
    ),         
    'turno' => array(
      'label'  => 'Turno',
      'help'   => '',
    ),      
  );


  protected function _preConstruct()
  {
    $this->_options = $this->mergeOptions(array('edit_success' => '/intranet/transporte_pessoa_lst.php','delete_success' => '/intranet/transporte_pessoa_lst.php'), $this->_options);
    $nomeMenu = $this->getRequest()->id == null ? "Cadastrar" : "Editar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_transporte_escolar_index.php"                  => "Transporte escolar",
         ""        => "$nomeMenu usu&aacute;rio de transporte"             
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
    $this->url_cancelar = '/intranet/transporte_pessoa_lst.php';

    // Código do vinculo
    $options = array('label'    => $this->_getLabel('id'), 'disabled' => true,
                     'required' => false, 'size' => 25);
    $this->inputsHelper()->integer('id', $options);

    // Pessoa
    $options = array('label' =>Portabilis_String_Utils::toLatin1($this->_getLabel('pessoa')), 'required' => true);
    $this->inputsHelper()->simpleSearchPessoa('nome',$options); 

    // Montar o inputsHelper->select \/
    // Cria lista de rotas 
    $obj_rota = new clsModulesRotaTransporteEscolar();
    $obj_rota->setOrderBy(' descricao asc ');
    $lista_rota = $obj_rota->lista();
    $rota_resources = array("" => "Selecione uma rota" );
    foreach ($lista_rota as $reg) {
      $rota_resources["{$reg['cod_rota_transporte_escolar']}"] = "{$reg['descricao']} - {$reg['ano']}";
    }
    
    // Rota
    $options = array('label' =>Portabilis_String_Utils::toLatin1($this->_getLabel('rota')), 'required' => true, 'resources' => $rota_resources);
    $this->inputsHelper()->select('rota',$options); 

    // Ponto de Embarque
    $options = array('label' =>Portabilis_String_Utils::toLatin1($this->_getLabel('ponto')), 'required' => false, 'resources' => array("" => "Selecione uma rota acima"));
    $this->inputsHelper()->select('ponto',$options);     

    // Destino
    $options = array('label' =>Portabilis_String_Utils::toLatin1($this->_getLabel('destino')), 'required' => false);
    $this->inputsHelper()->simpleSearchPessoaj('destino',$options); 

    // observacoes
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('observacao')), 'required' => false, 'size' => 50, 'max_length' => 255);
    $this->inputsHelper()->textArea('observacao', $options);     

 // turno
    $options = array('label' =>Portabilis_String_Utils::toLatin1($this->_getLabel('turno')), 'required' => false);
    $this->inputsHelper()->select('turno', array('required' => false, 'resources' => array( 0 => "Selecione", 1 => 'Matutino', 2 => 'Vespertino', 3 => 'Noturno', 4 => 'Integral')));



    $this->loadResourceAssets($this->getDispatcher());
  }
}
?>