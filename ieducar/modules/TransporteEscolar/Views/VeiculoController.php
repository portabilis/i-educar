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
require_once 'include/modules/clsModulesTipoVeiculo.inc.php';

class VeiculoController extends Portabilis_Controller_Page_EditController
{
  protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
  protected $_titulo     = 'i-Educar - Motoristas';

  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_processoAp        = 21237;
  protected $_deleteOption      = true;

  protected $_formMap    = array(
    'id' => array(
      'label'  => 'Código do veículo',
      'help'   => '',
    ),

    'descricao' => array(
      'label'  => 'Descrição',
      'help'   => '',
    ),

    'placa' => array(
      'label'  => 'Placa',
      'help'   => '',
    ),

    'renavam' => array(
      'label'  => 'Renavam',
      'help'   => '',
    ),

    'chassi' => array(
      'label'  => 'Chassi',
      'help'   => '',
    ),

    'marca' => array(
      'label'  => 'Marca',
      'help'   => '',
    ),

    'ano_fabricacao' => array(
      'label'  => 'Ano fabricação',
      'help'   => '',
    ),

    'ano_modelo' => array(
      'label'  => 'Ano modelo',
      'help'   => '',
    ),

    'passageiros' => array(
      'label'  => 'Limite de passageiros',
      'help'   => '',
    ),

    'tipo' => array(
      'label'  => 'Categoria',
      'help'   => '',
    ),


    'malha' => array(
      'label'  => 'Malha',
      'help'   => '',
    ),    

    'abrangencia' => array(
      'label'  => 'Abrangência',
      'help'   => '',
    ),

    'exclusivo_transporte_escolar' => array(
      'label'  => 'Exclusivo para transporte escolar',
      'help'   => '',
    ),                                    

    'adaptado_necessidades_especiais' => array(
      'label'  => 'Adaptado para pessoas com necessidades especiais',
      'help'   => '',
    ),

    'tercerizado' => array(
      'label'  => 'Tercerizado',
      'help'   => '',
    ),

    'ativo' =>array(
      'label'  => 'Ativo',
      'help'   => '',
    ),

    'descricao_inativo' =>array(
      'label'  => 'Descrição de inatividade',
      'help'   => '',
    ),

    'empresa' =>array(
      'label'  => 'Empresa',
      'help'   => '',
    ),

    'motorista' =>array(
      'label'  => 'Motorista responsável',
      'help'   => '',
    ),

    'observacao' =>array(
      'label'  => 'Observações',
      'help'   => '',
    )

  );


  protected function _preConstruct()
  {
    $this->_options = $this->mergeOptions(array('edit_success' => '/intranet/transporte_veiculo_lst.php','delete_success' => '/intranet/transporte_veiculo_lst.php'), $this->_options);
    $nomeMenu = $this->getRequest()->id == null ? "Cadastrar" : "Editar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_transporte_escolar_index.php"                  => "Transporte escolar",
         ""        => "$nomeMenu ve&iacute;culo"             
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
    $this->url_cancelar = '/intranet/transporte_veiculo_lst.php';

    // Código do Motorista
    $options = array('label'    => $this->_getLabel('id'), 'disabled' => true,
                     'required' => false, 'size' => 25);
    $this->inputsHelper()->integer('id', $options);

    // descrição    
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('descricao')), 'required' => true, 'size' => 50, 'max_length' => 255);
    $this->inputsHelper()->text('descricao', $options);

    //placa
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('placa')), 'required' => false, 'size' => 10, 'max_length' => 10);
    $this->inputsHelper()->text('placa', $options);     

    //renavam
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('renavam')), 'required' => false, 'size' => 15, 'max_length' => 15);
    $this->inputsHelper()->integer('renavam', $options);   

    //chassi
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('chassi')), 'required' => false, 'size' => 30, 'max_length' => 30);
    $this->inputsHelper()->text('chassi', $options);    

    //marca
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('marca')), 'required' => false, 'size' => 50, 'max_length' => 50);
    $this->inputsHelper()->text('marca', $options);                    

    //Ano de fabricacao
    $options = array('label' => $this->_getLabel('ano_fabricacao'), 'max_length' => 4, 'size' => 5, 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->integer('ano_fabricacao',$options);

    // Ano do modelo
    $options = array('label' => $this->_getLabel('ano_modelo'), 'max_length' => 4, 'size' => 5, 'required' => false, 'placeholder' => '');
    $this->inputsHelper()->integer('ano_modelo',$options);

    // Passageiros
    $options = array('label' => $this->_getLabel('passageiros'), 'max_length' => 3, 'size' => 5, 'required' => true, 'placeholder' => '');
    $this->inputsHelper()->integer('passageiros',$options);    

    // Malha
    $malhas = array(null           => 'Selecione uma Malha', 'A' => Portabilis_String_Utils::toLatin1('Aquaviária/Embarcação'),
      'F' => Portabilis_String_Utils::toLatin1('Ferroviária'), 'R' => Portabilis_String_Utils::toLatin1('Rodoviária'));

    $options = array('label'     => $this->_getLabel('malha'),
                     'resources' => $malhas,
                     'required'  => true);

    $this->inputsHelper()->select('malha', $options);

    // Tipo de veículo
    $tiposVeiculo = array( null => 'Selecione um Tipo');    

    $objTipo = new clsModulesTipoVeiculo();
    $lista = $objTipo->lista();
    if ( is_array( $lista ) && count( $lista ) )
    {
      foreach ( $lista as $registro )
      {
        $tiposVeiculo["{$registro['cod_tipo_veiculo']}"] = "{$registro['descricao']}";
      }
    }

    $options = array('label'     => $this->_getLabel('tipo'),
                     'resources' => $tiposVeiculo,
                     'required'  => true);

    $this->inputsHelper()->select('tipo', $options);    

    // Exclusivo transporte escolar
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('exclusivo_transporte_escolar')));
    $this->inputsHelper()->checkbox('exclusivo_transporte_escolar', $options);    

    // Adaptado a necessidades especiais
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('adaptado_necessidades_especiais')));
    $this->inputsHelper()->checkbox('adaptado_necessidades_especiais', $options);        

    // Ativo
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('ativo')), 'value' => 'on');
    $this->inputsHelper()->checkbox('ativo', $options);        

    // descricao_inativo    
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('descricao_inativo')), 'required' => false, 'size' => 50, 'max_length' => 155);
    $this->inputsHelper()->textArea('descricao_inativo', $options);


    // Codigo da empresa
    $options = array('label' =>Portabilis_String_Utils::toLatin1($this->_getLabel('empresa')), 'required' => true);
    $this->inputsHelper()->simpleSearchEmpresa('empresa',$options);  

    // Codigo do motorista
    $options = array('label' =>Portabilis_String_Utils::toLatin1($this->_getLabel('motorista')), 'required' => false);
    $this->inputsHelper()->simpleSearchMotorista('motorista',$options);      

    // observações    
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('observacao')), 'required' => false, 'size' => 50, 'max_length' => 255);
    $this->inputsHelper()->textArea('observacao', $options);

    $this->loadResourceAssets($this->getDispatcher());
  }

}
?>