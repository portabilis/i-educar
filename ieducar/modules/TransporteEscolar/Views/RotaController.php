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
 * @since     Arquivo disponível desde a versão ?21238
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/Page/EditController.php';
require_once 'Usuario/Model/FuncionarioDataMapper.php';

class RotaController extends Portabilis_Controller_Page_EditController
{
  protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
  protected $_titulo     = 'i-Educar - Rotas';

  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_processoAp        = 21238;
  protected $_deleteOption      = true;

  protected $_formMap    = array(

    'id' => array(
      'label'  => 'Código da rota',
      'help'   => '',
    ),
    'desc' => array(
      'label'  => 'Descrição',
      'help'   => '',
    ),
    'ref_idpes_destino' => array(
      'label'  => 'Instituição destino',
      'help'   => '',
    ),
    'ano' => array(
      'label'  => 'Ano',
      'help'   => '',
    ),
    'tipo_rota' => array(
      'label'  => 'Tipo da rota',
      'help'   => '',
    ),
    'km_pav' => array(
      'label'  => 'Km pavimentados',
      'help'   => '',
    ),
    'km_npav' => array(
      'label'  => 'Km não pavimentados',
      'help'   => '',
    ),
    'ref_cod_empresa_transporte_escolar' => array(
      'label'  => 'Empresa',
      'help'   => '',
    ),
    'tercerizado' => array(
      'label'  => 'Terceirizado',
      'help'   => '',
    )
  );


  protected function _preConstruct()
  {
    $this->_options = $this->mergeOptions(array('edit_success' => '/intranet/transporte_rota_lst.php','delete_success' => '/intranet/transporte_rota_lst.php'), $this->_options);
    $nomeMenu = $this->getRequest()->id == null ? "Cadastrar" : "Editar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_transporte_escolar_index.php"                  => "Transporte escolar",
         ""        => "$nomeMenu rota"             
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
    $this->url_cancelar = '/intranet/transporte_rota_lst.php';

    // ano
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('ano')), 'required' => true, 'size' => 5, 'max_length' => 4);
    $this->inputsHelper()->integer('ano', $options);

    // Código da rota
    $options = array('label'    => $this->_getLabel('id'), 'disabled' => true,
                     'required' => false, 'size' => 25);
    $this->inputsHelper()->integer('id', $options);

    // descricao
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc')), 'required' => true, 'size' => 50, 'max_length' => 50);
    $this->inputsHelper()->text('desc', $options);

    // Destino
    $options = array('label' => $this->_getLabel('ref_idpes_destino'), 'required' => true, 'size' => 50);
    $this->inputsHelper()->simpleSearchPessoaj('ref_idpes_destino', $options);


    // Empresa rota
    $options = array('label' => $this->_getLabel('ref_cod_empresa_transporte_escolar'), 'required' => true, 'size' => 50);
    $this->inputsHelper()->simpleSearchEmpresa('ref_cod_empresa_transporte_escolar', $options);    

    // Tipo
    $tipos = array(null           => 'Selecione um tipo', 'U' => 'Urbana',
       'R' => 'Rural');

    $options = array('label'     => $this->_getLabel('tipo_rota'),
                     'resources' => $tipos,
                     'required'  => true);

    $this->inputsHelper()->select('tipo_rota', $options);

    // km pavimentados
    $options = array('label' => $this->_getLabel('km_pav'), 'required' => false, 'size' => 9, 'max_length' => 10, 'placeholder' => '');
    $this->inputsHelper()->numeric('km_pav', $options);

    // km não pavimentados
    $options = array('label' => $this->_getLabel('km_npav'), 'required' => false, 'size' => 9, 'max_length' => 10, 'placeholder' => '');
    $this->inputsHelper()->numeric('km_npav', $options);

     // Tercerizado
    $options = array('label' => $this->_getLabel('tercerizado'));
    $this->inputsHelper()->checkbox('tercerizado', $options);        




    $this->loadResourceAssets($this->getDispatcher());
  }

}
?>