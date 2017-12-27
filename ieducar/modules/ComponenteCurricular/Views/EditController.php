<?php

// error_reporting(E_ALL);
// ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
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
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'Core/Controller/Page/EditController.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
require_once 'ComponenteCurricular/Model/TipoBase.php';
require_once 'ComponenteCurricular/Model/CodigoEducacenso.php';

/**
 * EditController class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class EditController extends Core_Controller_Page_EditController
{
  protected $_dataMapper        = 'ComponenteCurricular_Model_ComponenteDataMapper';
  protected $_titulo            = 'Cadastro de componente curricular';
  protected $_processoAp        = 946;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::INSTITUCIONAL;
  protected $_saveOption        = TRUE;
  protected $_deleteOption      = FALSE;

  protected $_formMap = array(
    'instituicao' => array(
      'label'  => 'Instituição',
      'help'   => '',
    ),
    'nome' => array(
      'label'  => 'Nome',
      'help'   => 'Nome por extenso do componente.',
    ),
    'abreviatura' => array(
      'label'  => 'Nome abreviado',
      'help'   => 'Nome abreviado do componente.',
      'entity' => 'abreviatura'
    ),
    'tipo_base' => array(
      'label'  => 'Base curricular',
      'help'   => '',
      'entity' => 'tipo_base'
    ),
    'area_conhecimento' => array(
      'label'  => 'Área conhecimento',
      'help'   => '',
      'entity' => 'area_conhecimento'
    ),

    'codigo_educacenso' => array(
      'label'  => 'Disciplina Educacenso',
      'help'   => '',
      'entity' => 'codigo_educacenso'
    ),
    'ordenamento' => array(
      'label'  => 'Ordem de apresentação',
      'help'   => 'Ordem respeitada no lançamento de notas/faltas.',
      'entity' => 'ordenamento'
    ),
  );

  protected function _preRender()
  {
    parent::_preRender();

    Portabilis_View_Helper_Application::loadStylesheet($this, 'intranet/styles/localizacaoSistema.css');

    $nomeMenu = $this->getRequest()->id == null ? "Cadastrar" : "Editar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Escola",
         ""        => "$nomeMenu componente curricular"
    ));
    $this->enviaLocalizacao($localizacao->montar());
  }

  /**
   * @see clsCadastro#Gerar()
   */
  public function Gerar()
  {
    $this->campoOculto('id', $this->getEntity()->id);

    // Instituição
    $instituicoes = App_Model_IedFinder::getInstituicoes();
    $this->campoLista('instituicao', $this->_getLabel('instituicao'),
      $instituicoes, $this->getEntity()->instituicao);

    // Nome
    $this->campoTexto('nome', $this->_getLabel('nome'), trim($this->getEntity()->nome),
      50, 500, TRUE, FALSE, FALSE, $this->_getHelp('nome'));

    // Abreviatura
    $this->campoTexto('abreviatura', $this->_getLabel('abreviatura'),
      $this->getEntity()->abreviatura, 50, 25, TRUE, FALSE,
      FALSE, $this->_getHelp('abreviatura'));

    // Tipo Base
    $tipoBase = ComponenteCurricular_Model_TipoBase::getInstance();
    $this->campoRadio('tipo_base', $this->_getLabel('tipo_base'),
      $tipoBase->getEnums(), $this->getEntity()->get('tipo_base'));

    // Área de conhecimento
    $areas = $this->getDataMapper()->findAreaConhecimento();
    $areas = CoreExt_Entity::entityFilterAttr($areas, 'id', 'nome');
    $this->campoLista('area_conhecimento', $this->_getLabel('area_conhecimento'),
      $areas, $this->getEntity()->get('area_conhecimento'));

    // Código educacenso
    $codigos = ComponenteCurricular_Model_CodigoEducacenso::getInstance();
    $this->campoLista('codigo_educacenso', $this->_getLabel('codigo_educacenso'),
      $codigos->getEnums(), $this->getEntity()->get('codigo_educacenso'));

    // Ordenamento
    $this-> campoNumero('ordenamento',
                        $this->_getLabel('ordenamento'),
                        $this->getEntity()->ordenamento==99999 ? null : $this->getEntity()->ordenamento,
                        15,
                        15,
                        false,
                        $this->_getHelp('ordenamento'));
  }

  /**
   * OVERRIDE
   * Insere um novo registro no banco de dados e redireciona para a página
   * definida pela opção "new_success".
   * @see clsCadastro#Novo()
   */
  public function Novo()
  {
    if ($this->_save()) {
      header("Location: /intranet/educar_componente_curricular_lst.php");
    }
    return FALSE;
  }

  protected function _save(){
    $data = array();

    foreach ($_POST as $key => $val) {
      if (array_key_exists($key, $this->_formMap)) {

        if($key == "ordenamento"){

          if((trim($val) == "") || (is_null($val))) {
            $data[$key] = 99999;
            continue;
          }
        }

        $data[$key] = $val;
      }
    }


    // Verifica pela existência do field identity
    if (isset($this->getRequest()->id) && 0 < $this->getRequest()->id) {
      $entity = $this->setEntity($this->getDataMapper()->find($this->getRequest()->id));
    }

    if (isset($entity)) {
      $this->getEntity()->setOptions($data);
    }
    else {
      $this->setEntity($this->getDataMapper()->createNewEntityInstance($data));
    }

    try {
      $this->getDataMapper()->save($this->getEntity());
      return TRUE;
    }
    catch (Exception $e) {
      // TODO: ver @todo do docblock
      $this->mensagem = 'Erro no preenchimento do formulário. ';
      return FALSE;
    }
  }
}
//seta o radio automaticamente como primeiro valor
echo '<script type="text/javascript">
      document.ready = function(){
      element = document.getElementById("tipo_base");
      element.checked = true;
      };
      </script>';
