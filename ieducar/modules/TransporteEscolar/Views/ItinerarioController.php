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

require_once 'include/modules/clsModulesRotaTransporteEscolar.inc.php';
require_once 'lib/Portabilis/Controller/Page/EditController.php';
require_once 'Usuario/Model/FuncionarioDataMapper.php';

class ItinerarioController extends Portabilis_Controller_Page_EditController
{
  protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
  protected $_titulo     = 'Cadastro de Rota';

  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_processoAp        = 578;
  protected $_deleteOption      = false;

  protected $_formMap    = array(

    'id' => array(
      'label'  => 'Código da rota',
      'help'   => '',
    ),
    'descricao' => array(
      'label'  => 'Descrição',
      'help'   => '',
    )
  );


  protected function _preConstruct()
  {
    $this->_options = $this->mergeOptions(array('edit_success' => '/intranet/transporte_rota_lst.php','delete_sucess' => '/intranet/transporte_rota_lst.php'), $this->_options);
  }


  protected function _initNovo() {
    return false;
  }


  protected function _initEditar() {
    return false;
  }


  public function Gerar()
  {
    $id = (isset($_GET['id']) ? $_GET['id'] : 0) ;
    if ($id==0 || !$this->verificaIdRota($id))
      header('Location: /intranet/transporte_rota_lst.php');
    
    $this->url_cancelar = '/intranet/transporte_rota_det.php?cod_rota='.$id.'';

    // Código da rota
    $options = array('label'    => $this->_getLabel('id'), 'disabled' => true,
                     'required' => false, 'size' => 25);
    $this->inputsHelper()->integer('id', $options);

    // descricao
    $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('descricao')), 'disabled' => true, 'size' => 50, 'max_length' => 50);
    $this->inputsHelper()->text('descricao', $options);
    $resourceOptionsTable = "
    <table id='disciplinas-manual'>
      <tr>
        <th>Ponto</th>
        <th>Hora</th>
        <th>Tipo</th> 
        <th>Veiculo</th>
        
        <th>A&ccedil;&atilde;o</th>
      </tr>
      <tr class='ponto'>
        <td><input class='nome obrigatorio disable-on-search change-state-with-parent'></input></td>
        <td><input class='nota' ></input></td>
        <td>
          <select id='disciplinas' name='disciplinas' class='obrigatorio disable-on-search'>
            <option value=''>Selecione</option>
            <option value='I'>Ida</option>
            <option value='V'>Volta</option>
          </select>
        </td>              
        <td>
          <input class='nome obrigatorio disable-on-search change-state-with-parent'></input>
        </td>
  
        <td>
          <a class='remove-disciplina-line' href='#'>Remover</a>
        </td>
      </tr>
<tr class='disciplina'>
        <td><input class='nome obrigatorio disable-on-search change-state-with-parent'></input></td>
        <td><input class='nota' ></input></td>
        <td>
          <select id='disciplinas' name='disciplinas' class='obrigatorio disable-on-search'>
            <option value=''>Selecione</option>
            <option value='I'>Ida</option>
            <option value='V'>Volta</option>
          </select>
        </td>              
        <td>
          <input class='nome obrigatorio disable-on-search change-state-with-parent'></input>
        </td>
  
        <td>
          <a class='remove-disciplina-line' href='#'>Remover</a>
        </td>
      </tr>      
      <tr class='actions'>
        <td colspan='4'>
          <input type='button' class='action' id='new-disciplina-line' name='new-line' value='Adicionar ponto'></input>
        </td>
      </tr>
    </table>";
    
    $this->appendOutput($resourceOptionsTable);

    //$this->loadResourceAssets($this->getDispatcher());
  }

  function verificaIdRota($id){
      $obj = new clsModulesRotaTransporteEscolar($id);
      return $obj->existe();
  }

}
?>